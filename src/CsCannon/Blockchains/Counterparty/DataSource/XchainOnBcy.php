<?php

namespace CsCannon\Blockchains\Counterparty\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContract;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\ContractMetaData;
use CsCannon\SandraManager;
use SandraCore\DatabaseAdapter;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class XchainOnBcy extends BlockchainDataSource
{

    public static $dbHost, $db, $dbpass, $dbUser;

    public static function getRawBalance(BlockchainAddress $address, $limit = 5000, $offset = 0): ForeignEntityAdapter
    {
        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT * FROM `index_addresses` 
                JOIN balances ON balances.address_id = index_addresses.id
                JOIN assets ON balances.asset_id = assets.id
                WHERE index_addresses.address = '" . $address->getAddress() . "'
                AND balances.quantity > 0" . " limit " . $limit . " offset " . $offset;

        $pdo = $blockSucker->get();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {
            System::sandraException($exception);
            return [];
        }

        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        $foreignAdapter = new ForeignEntityAdapter("", '', SandraManager::getSandra(), null, $resultArray);
        $foreignAdapter->populate();

        return $foreignAdapter;
    }

    public static function getEventsFromTimestamp(string $timestamp, $contract = null, $batchMax = 1000, $offset = 0)
    {
        $foreignEntityAdapter = new ForeignEntityAdapter(null, '', SandraManager::getSandra());

        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);

        $sql = "SELECT sends.tx_index, b.block_time, sends.block_index, hash as tx_hash, s.address as source_address,  d.address as destination_address, a.asset as asset, `quantity`, memo FROM sends
            JOIN index_transactions ON sends.`tx_hash_id` = index_transactions.id
            JOIN index_addresses s ON sends.`source_id` = s.id
            JOIN index_addresses d ON sends.`destination_id` = d.id
            JOIN assets a  ON sends.`asset_id` = a.id
            JOIN blocks b  ON sends.`block_index` = b.`block_index`
            WHERE b.block_time > $timestamp
            ORDER BY b.block_time
            LIMIT $batchMax OFFSET $offset
            ";


        $pdo = $blockSucker->get();
        $entityArray = array();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch (PDOException $exception) {
            System::sandraException($exception);
            return $foreignEntityAdapter;
        }

        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($resultArray)) {
            $foreignEntityAdapter->entityArray = [];
            return $foreignEntityAdapter;
        }

        foreach ($resultArray as $result) {

            if (!($result['asset'])) {
                $result['asset'] = 'NOASSET';
                continue;
            }

            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES] = array();
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['source_address'];
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['destination_address'];

            $hash = $result['tx_hash'];

            $quantity = $result['quantity'];
            $transactionData = array("txHash" => "$hash",
                "memo" => $result['memo'],
                "quantity" => $quantity,
                Blockchain::$txidConceptName => $hash,
                BlockchainEventFactory::EVENT_SOURCE_ADDRESS => $result['source_address'],
                BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB => $result['destination_address'],
                "tokenId" => $result['asset'],
                "blockIndex" => $result['block_index'],
                BlockchainEventFactory::EVENT_BLOCK_TIME => $result['block_time'],
                BlockchainImporter::TRACKER_BLOCKTIME => $result['block_time'],
                BlockchainEventFactory::EVENT_CONTRACT => $result['asset'],
            );

            //add tracker
            $transactionData[BlockchainImporter::TRACKER_CONTRACTIDS][] = $result['asset'];
            $transactionData = $transactionData + $trackedArray;

            $entityArray[] = new ForeignEntity($hash, $transactionData, $foreignEntityAdapter, $hash, SandraManager::getSandra());
        }

        $foreignEntityAdapter->addNewEtities($entityArray, array());

        return $foreignEntityAdapter;
    }


    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {


        $foreignEntityAdapter = new ForeignEntityAdapter(null, '', SandraManager::getSandra());

        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT sends.tx_index, b.block_time, sends.block_index, hash as tx_hash, s.address as source_address,  d.address as destination_address, a.asset as asset, `quantity`, memo FROM sends 
JOIN index_transactions ON sends.`tx_hash_id` = index_transactions.id
JOIN index_addresses s ON sends.`source_id` = s.id
JOIN index_addresses d ON sends.`destination_id` = d.id
JOIN assets a  ON sends.`asset_id` = a.id
JOIN blocks b  ON sends.`block_index` = b.`block_index`
         
         LIMIT $batchMax OFFSET $offset";
        $pdo = $blockSucker->get();

        $entityArray = array();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {

            System::sandraException($exception);
            return $foreignEntityAdapter;
        }


        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        return self::processEvents($resultArray);
    }

    private static function processEvents($resultArray)
    {
        $foreignEntityAdapter = new ForeignEntityAdapter(null, '', SandraManager::getSandra());
        $entityArray = array();
        foreach ($resultArray as $result) {

            if (!($result['asset'])) {

                $result['asset'] = 'NOASSET';
                continue;
            }
            //echo"asset :".$result['asset'] .PHP_EOL;


            //add tracker
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES] = array();

            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['source_address'];
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['destination_address'];


            $hash = $result['tx_hash'];

            $quantity = $result['quantity'];
            $transactionData = array("txHash" => "$hash",
                "memo" => $result['memo'],
                "quantity" => $quantity,
                Blockchain::$txidConceptName => $hash,
                BlockchainEventFactory::EVENT_SOURCE_ADDRESS => $result['source_address'],
                BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB => $result['destination_address'],
                "tokenId" => $result['asset'],
                "blockIndex" => $result['block_index'],
                BlockchainEventFactory::EVENT_BLOCK_TIME => $result['block_time'],
                BlockchainImporter::TRACKER_BLOCKTIME => $result['block_time'],
                BlockchainEventFactory::EVENT_CONTRACT => $result['asset'],


            );
            //add tracker
            $transactionData[BlockchainImporter::TRACKER_CONTRACTIDS][] = $result['asset'];


            //$transactionData[BlockchainImporter::TRACKER_BLOCKID][] =  $result['block_index'] ;


            $transactionData = $transactionData + $trackedArray;

            $entityArray[] = $entity = new ForeignEntity($hash, $transactionData, $foreignEntityAdapter, $hash, SandraManager::getSandra());


        }

        $foreignEntityAdapter->addNewEtities($entityArray, array());

        return $foreignEntityAdapter;

    }

    public static function getEventsFromTxHash($txHashArray): ForeignEntityAdapter
    {
        $foreignEntityAdapter = new ForeignEntityAdapter(null, '', SandraManager::getSandra());

        $commaSeparated = '';
        $first = true;
        foreach ($txHashArray as $hasString) {

            $first ? $commaSeparated .= "'$hasString'" : $commaSeparated .= ",'$hasString'";

            $first = false;
        }

        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT sends.tx_index, b.block_time, sends.block_index, hash as tx_hash, s.address as source_address,  d.address as destination_address, a.asset as asset, `quantity`, memo FROM sends
JOIN index_transactions ON sends.`tx_hash_id` = index_transactions.id
JOIN index_addresses s ON sends.`source_id` = s.id
JOIN index_addresses d ON sends.`destination_id` = d.id
JOIN assets a  ON sends.`asset_id` = a.id
JOIN blocks b  ON sends.`block_index` = b.`block_index` WHERE hash IN ($commaSeparated)";
        $pdo = $blockSucker->get();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {

            System::sandraException($exception);
            return $foreignEntityAdapter;
        }


        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        return self::processEvents($resultArray);


    }

    public static function getExchanges($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {


        $foreignEntityAdapter = new ForeignEntityAdapter(null, '', SandraManager::getSandra());

        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT sends.tx_index, b.block_time, sends.block_index, hash as tx_hash, s.address as source_address,  d.address as destination_address, a.asset as asset, `quantity`, memo FROM sends 
JOIN index_transactions ON sends.`tx_hash_id` = index_transactions.id
JOIN index_addresses s ON sends.`source_id` = s.id
JOIN index_addresses d ON sends.`destination_id` = d.id
JOIN assets a  ON sends.`asset_id` = a.id
JOIN blocks b  ON sends.`block_index` = b.`block_index`
         
         LIMIT $batchMax OFFSET $offset";
        $pdo = $blockSucker->get();

        $entityArray = array();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {

            System::sandraException($exception);
            return $foreignEntityAdapter;
        }

        $array = array();


        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);


        return self::processEvents($resultArray);
    }

    public static function getContractMetaData($contract): ContractMetaData

    {

        $metadata = new ContractMetaData($contract);


        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT asset as contract_id, 
divisible, supply, block_index, 'type',asset_longname, locked,
a.address as issuer_address, 
a2.address as owner_address FROM assets
         JOIN index_addresses as a on assets.issuer_id = a.id
          JOIN index_addresses as a2 on assets.owner_id = a2.id
           WHERE assets.asset = '" . $contract->getId() . "'
         ";
        $pdo = $blockSucker->get();

        $entityArray = array();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {

            System::sandraException($exception);
            return $metadata;
        }


        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($resultArray as $result) {
            $decimals = 0;
            if ($result['divisible'] == 1) $decimals = 8;

            $array = array();

            $metadata->setIsMutableSupply($result['locked'] ? 0 : 1);
            $metadata->setDecimals($decimals);
            $metadata->setInterface(CounterpartyAsset::init());
            $metadata->setTotalSupply($result['supply']);
            //echo"asset :".$result['asset'] .PHP_EOL;


        }

        return $metadata;
    }


    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        $limit = 5000;

        $limitSQL = '';
        $offsetSql = '';


        if ($limit)
            $limitSQL = "LIMIT  $limit";

        if ($offset)
            $offsetSql = "OFFSET = $offset";

        $blockSucker = new PdoConnexionWrapper(self::$dbHost, self::$db, self::$dbUser, self::$dbpass);
        $sql = "SELECT * FROM `index_addresses` 
JOIN balances ON balances.address_id = index_addresses.id
JOIN assets ON balances.asset_id = assets.id
WHERE index_addresses.address = '" . $address->getAddress() . "'
AND balances.quantity > 0
         
         $limitSQL $offsetSql";
        $pdo = $blockSucker->get();


        $balance = new Balance($address);
        $entityArray = array();

        try {
            $pdoResult = $pdo->prepare($sql);
            $pdoResult->execute();
        } catch
        (PDOException $exception) {

            System::sandraException($exception);
            return $balance;
        }

        $contractNames = array();
        $balanceData = array();

        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($resultArray ?? array() as $result) {

            if (!($result['asset'])) {

                $result['asset'] = 'NOASSET';
                continue;
            }

            $contractNames[] = $result['asset'];
            $balanceData[$result['asset']] = $result;


        }
        //we preload the contractFactory
        $counterPartyContractFactory = new XcpContractFactory();
        $preloadConcepts = DatabaseAdapter::searchConcept($counterPartyContractFactory->system, $contractNames,
            XcpContractFactory::MAIN_IDENTIFIER, 0, XcpContractFactory::$file);


        //If we have existing counterparty contracts
        if ($preloadConcepts) {
            $counterPartyContractFactory->conceptArray = $preloadConcepts;
            $counterPartyContractFactory->populateLocal();
        }

        $conterpartyAsset = CounterpartyAsset::init();
        foreach ($balanceData as $contractName => $value) {

            /** @var XcpContract $contract */
            $contract = $counterPartyContractFactory->get($contractName, true);

            $contract->setXcpPrice($value['xcp_price'] ?? 0);
            $contract->setBtcPrice($value['btc_price'] ?? 0);

            $balanceValue = $value['quantity'];


            if ($value['divisible']) $contract->setDivisible();


            $balance->addContractToken($contract, $conterpartyAsset, $balanceValue);
        }


        //$balance->addContractToken($contract,$conterpartyAsset,$entity->get('balance'));


        return $balance;

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/" . $address->getAddress(), 'data', SandraManager::getSandra());

        $foreignAdapter->adaptToLocalVocabulary(array('asset' => 'contractId',
            'quantity' => 'balance'));
        $foreignAdapter->populate();

        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();

        foreach ($foreignAdapter->entityArray as $entity) {

            /** @var ForeignEntity $entity */
            $contract = $cpContracts->get($entity->get('contractId'), true);


            $balance->addContractToken($contract, $conterpartyAsset, $entity->get('balance'));

        }

        return $balance;


    }
}
