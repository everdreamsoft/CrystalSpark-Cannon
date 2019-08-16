<?php

namespace CsCannon\Blockchains\Counterparty\DataSource;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class XchainOnBcy extends BlockchainDataSource
{

    public $sandra ;

    public static $dbHost, $db, $dbpass, $dbUser ;




    public function getEvents($contract,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter
    {


        $foreignEntityAdapter = new ForeignEntityAdapter(null,'',$this->sandra);

        $blockSucker = new PdoConnexionWrapper(self::dbHost, self::db, self::dbUser, self::dbpass);
        $sql = "SELECT sends.tx_index, b.block_time, sends.block_index, hash as tx_hash, s.address as source_address,  d.address as destination_address, a.asset as asset, `quantity`, memo FROM sends 
JOIN index_transactions ON sends.`tx_index` = index_transactions.id
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
                "blockTime" => $result['block_time'],
                "contract" => $result['asset'],


            );

            //add tracker
            $transactionData[BlockchainImporter::TRACKER_CONTRACTIDS][] = $result['asset'] ;


            //$transactionData[BlockchainImporter::TRACKER_BLOCKID][] =  $result['block_index'] ;


            $transactionData = $transactionData + $trackedArray ;

            $entityArray[] = $entity = new ForeignEntity($hash, $transactionData, $foreignEntityAdapter, $hash, $this->sandra);


        }

        $foreignEntityAdapter->addNewEtities($entityArray,array());

        return $foreignEntityAdapter ;
    }





}