<?php

namespace CsCannon\Blockchains\Counterparty\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\SandraManager;
use PDOException;
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
class XchainDataSource extends BlockchainDataSource
{

    public static $chainUrl;
    public static $defaultChainUrl = "https://xchain.io/api/balances/";


    public static $dbHost, $db, $dbpass, $dbUser ;


    public function __construct(string $net = null){

        if($net === "testnet"){
            self::$chainUrl = "https://testnet.xchain.io/api/balances/";
        }

    }



    public static function getEvents($contract=null,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter
    {


        $foreignEntityAdapter = new ForeignEntityAdapter(null,'',SandraManager::getSandra());

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
            return null;
        }

        $array = array();


        $resultArray = $pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($resultArray as $result) {

            if (!($result['asset'])){

                $result['asset'] ='NOASSET' ;
                continue ;
            }
            //echo"asset :".$result['asset'] .PHP_EOL;


            //add tracker
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES] = array();

            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['source_address'] ;
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['destination_address'] ;



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
            $transactionData[BlockchainImporter::TRACKER_CONTRACTIDS][] = $result['asset'] ;


            //$transactionData[BlockchainImporter::TRACKER_BLOCKID][] =  $result['block_index'] ;


            $transactionData = $transactionData + $trackedArray ;

            $entityArray[] = $entity = new ForeignEntity($hash, $transactionData, $foreignEntityAdapter, $hash, SandraManager::getSandra());


        }

        $foreignEntityAdapter->addNewEtities($entityArray,array());

        return $foreignEntityAdapter ;
    }


    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {


        $foreignAdapter = new ForeignEntityAdapter(self::getChainUrl().$address->getAddress(),'data',SandraManager::getSandra());

        $foreignAdapter->adaptToLocalVocabulary(array('asset'=>'contractId',
            'quantity'=>'balance'));
        $foreignAdapter->populate();

        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();

        foreach ($foreignAdapter->entityArray as $entity) {

            /** @var ForeignEntity $entity */
            $contract = $cpContracts->get($entity->get('contractId'),true);


            $balance->addContractToken($contract,$conterpartyAsset,$entity->get('balance'));

        }

        return $balance;


    }

    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit, $offset): Balance
    {


        $foreignAdapter = new ForeignEntityAdapter(self::getChainUrl().$address->getAddress(),'data',SandraManager::getSandra());

        $foreignAdapter->adaptToLocalVocabulary(array('asset'=>'contractId',
            'quantity'=>'balance'));
        $foreignAdapter->populate();

        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();

        $contractAddressMap = [];

        foreach ($contract ?? [] as $contract){

            //we remove unwanted contracts
            if (!isset($contractAddressMap[$contract->getId()])) continue ;


            $contractAddressMap[$contract->getId()] = $contract; ;
        }

        foreach ($foreignAdapter->entityArray as $entity) {

            /** @var ForeignEntity $entity */
            $contract = $cpContracts->get($entity->get('contractId'),true);


            $balance->addContractToken($contract,$conterpartyAsset,$entity->get('balance'));

        }

        return $balance;


    }

    public static function broadcastTx($rawHex)
    {

        $ch       = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://xchain.io/api/send_tx');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'tx_hex='. $rawHex);
//curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $result = curl_exec($ch);
if ($result === false) {
    throw new \Exception(curl_error($ch), curl_errno($ch));
}
        curl_close($ch);

        print_r (json_decode($result));

        return $result->rawHex ;




    }

        public static function getChainUrl(): String
    {

       if (!isset(self::$chainUrl)){

           self::$chainUrl = self::$defaultChainUrl ;

       }

       return self::$chainUrl;


    }

}