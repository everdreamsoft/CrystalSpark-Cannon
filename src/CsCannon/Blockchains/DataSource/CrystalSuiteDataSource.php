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
use SandraCore\DatabaseAdapter;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class CrystalSuiteDataSource extends BlockchainDataSource
{



    public static $dbHost, $db, $dbpass, $dbUser ;




    public static function getEvents($contract=null,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter
    {

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$address->getAddress(),'data',
            SandraManager::getSandra());

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$address->getAddress(),'data',SandraManager::getSandra());

        //$foreignEntityAdapter->addNewEtities($entityArray,array());

        return $foreignAdapter ;
    }


    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

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
WHERE index_addresses.address = '".$address->getAddress()."'
         
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
            return null;
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
            $balanceData[$result['asset']] = $result ;


        }
        //we preload the contractFactory
        $counterPartyContractFactory  = new XcpContractFactory();
        $preloadConcepts = DatabaseAdapter::searchConcept($contractNames,
            XcpContractFactory::MAIN_IDENTIFIER,$counterPartyContractFactory->system,0,XcpContractFactory::$file);


        //If we have existing counterparty contracts
        if ($preloadConcepts) {
            $counterPartyContractFactory->conceptArray = $preloadConcepts;
            $counterPartyContractFactory->populateLocal();
        }

        $conterpartyAsset = CounterpartyAsset::init();
        foreach ($balanceData as $contractName => $value){

            $contract = $counterPartyContractFactory->get($contractName,true);
            $balanceValue = $value['quantity'] ;
           if ($value['divisible']) $contract->setDivisible();


            $balance->addContractToken($contract,$conterpartyAsset,$balanceValue);
        }




        //$balance->addContractToken($contract,$conterpartyAsset,$entity->get('balance'));





            return $balance ;

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$address->getAddress(),'data',SandraManager::getSandra());

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
}