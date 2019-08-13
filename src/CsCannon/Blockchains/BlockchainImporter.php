<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:50
 */

namespace CsCannon\Blockchains;


use SandraCore\DatabaseAdapter;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

abstract class BlockchainImporter
{

    //tracker are the joined entity needed to be added or recovered conceptIds
    //changing this will impact runtime not datagraph
    CONST TRACKER_ADDRESSES = 'tracked.addresses';
    CONST TRACKER_CONTRACTIDS = 'tracked.tokenIds' ;
    CONST TRACKER_BLOCKID = 'blockIndex' ;
    CONST TRACKER_BLOCKTIME = 'blockTime' ;

    public $dataSource = null ;
    public $defaultDataSource = BlockchainDataSource::class ;
    public $sandra ;
    public $blockchain = Blockchain::class;

    public $responseArray = array();

    public $dataImportOffset = 0 ;




    public  function eventImport($dataSource = 'default'){






    }
    public function __construct(System $sandra)
    {

        $this->sandra = $sandra ;
        $this->blockchain = new $this->blockchain();

    }

    public function getDataSource($dataSource): BlockchainDataSource{

        if ($dataSource == 'default' or $dataSource === null){

            $this->dataSource = new $this->defaultDataSource($this->sandra);

            return $this->dataSource;

        }




    }


    public function saveEvents(ForeignEntityAdapter $foreignAdapter,
                               Blockchain $blockchain,
                               $contractFactory,
                               BlockchainAddressFactory $addressFactory,
                               BlockchainBlockFactory $blockfactory ){


        /** @var BlockchainEventFactory $populateEventFactory */



        $run_time_start = microtime(true);

        $eventFactory = clone $blockchain->getEventFactory();
        $populateEventFactory = clone $blockchain->getEventFactory(); //we populate a factory with existing events

        //find existing events

        $txToFindArray = array();

        foreach ($foreignAdapter->entityArray as $entity) {

            /** @var BlockchainEventFactory $eventFactory */
            $txToFind = $entity->get(Blockchain::$txidConceptName) ;
            $txToFindArray[] = $txToFind ;

        }

        //how many address in the map ?
        $countTotalEntities = count($txToFindArray);

        $txidConceptUnid = $this->sandra->systemConcept->get(Blockchain::$txidConceptName);
        $fileUnid = $this->sandra->systemConcept->get(BlockchainEventFactory::$file);

        $conceptsArray = DatabaseAdapter::searchConcept($txToFindArray,$txidConceptUnid,$this->sandra,'',$fileUnid);
        $populateEventFactory->conceptArray = $conceptsArray ;//we preload the factory with found concepts

        $matchingEntities = 0 ;

        if(!empty($conceptsArray)) {
            $populateEventFactory->populateLocal();

        }
        else {
            //this takes a lot of time
            $populateEventFactory->populateLocal(1,0,null);

        }
        $matchingEntities = count($addressFactory->entityArray);



        $time_end = microtime(true);
        $searchTime = $time_end - $run_time_start;

        $run_time_start = microtime(true);


        foreach ($foreignAdapter->entityArray as $fentity){

            $foundEntity = $populateEventFactory->first(Blockchain::$txidConceptName,$fentity->get(Blockchain::$txidConceptName)) ;

            if (is_null($foundEntity)){

                $sourceAddress = $addressFactory->get($fentity->get(BlockchainEventFactory::EVENT_SOURCE_ADDRESS));
                $destination = $addressFactory->get($fentity->get(BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB));
                $tx = $fentity->get(Blockchain::$txidConceptName);

                $contract = $contractFactory->get($fentity->get(BlockchainEventFactory::EVENT_CONTRACT));
                $blockTime = $fentity->get(BlockchainEventFactory::EVENT_BLOCK_TIME);
                $block = $blockfactory->get($fentity->get(BlockchainBlockFactory::INDEX_SHORTNAME)); //to verify

                $tokenId = $fentity->get(BlockchainContractFactory::TOKENID);

                $populateEventFactory->create($blockchain,$sourceAddress,$destination,$contract,$tx,$blockTime,$block,$tokenId);


            }


        }

        $time_end = microtime(true);
        $insertTime = $time_end - $run_time_start;

        $responseArray['new'] = $countTotalEntities - $matchingEntities ;
        $responseArray['existing'] =  $matchingEntities ;
        $responseArray['total'] =  $countTotalEntities ;
        $responseArray['time']['insert']['total'] =  $insertTime ;
        $responseArray['time']['search']['total'] =  $searchTime ;

        $this->responseArray['events'] = $responseArray ;

        $this->dataImportOffset = count($foreignAdapter->entityArray);




    }



    public function getPopulatedAddressFactory(ForeignEntityAdapter $foreignAdapter){




        $run_time_start = microtime(true);

        /** @var Blockchain $blockchain */
        $blockchain = $this->blockchain ;
        $addressFactory = clone $blockchain->getAddressFactory();
        /** @var BlockchainAddressFactory $addressFactory */

        $addressesToFindArray = array();
        $addressListMap = array();


        foreach ($foreignAdapter->entityArray as $entity){


            $addressesToFind = $entity->get(self::TRACKER_ADDRESSES) ;
            $addressesToFindArray[] = $addressesToFind ;
            //each addressTracker might have multiple address
            foreach ($addressesToFind as $addressString){
                $addressListMap[$addressString] = $addressString ;
            }
        }



        //how many address in the map ?
        $countTotalAddress = count($addressListMap);

        //convert in numeric array
        $addressList = array_values($addressListMap);

        $addressUnid = $this->sandra->systemConcept->get($addressFactory::ADDRESS_SHORTNAME);
        $fileUnid = $this->sandra->systemConcept->get($addressFactory::$file);

        //we search concepts with existing addresses
        $conceptsArray = DatabaseAdapter::searchConcept($addressList,$addressUnid,$this->sandra,'',$fileUnid);

        $addressFactory->conceptArray = $conceptsArray ;//we preload the factory with found concepts
        if(!empty($conceptsArray)) {
            $addressFactory->populateLocal();
        }

        $time_end = microtime(true);
        $searchTime = $time_end - $run_time_start;

        $run_time_start = microtime(true);


        //how many maching entities
        $matchingEntities = count($addressFactory->entityArray);

        $responseArray['new'] = $countTotalAddress - $matchingEntities ;
        $responseArray['existing'] =  $matchingEntities ;
        $responseArray['total'] =  $countTotalAddress ;


        foreach ($addressList as $addressString){

            $foundAddress = $addressFactory->get($addressString,true);
        }

        $time_end = microtime(true);
        $insertTime = $time_end - $run_time_start;

        $run_time_start = microtime(true);

        $responseArray['time']['insert']['total'] =  $insertTime ;
        $responseArray['time']['search']['total'] =  $searchTime ;

        $this->responseArray['addresses'] = $responseArray ;


        return  $addressFactory ;





    }

    public function getPopulatedContractFactory(ForeignEntityAdapter $foreignAdapter){


        /** @var Blockchain $blockchain */
        $blockchain = $this->blockchain ;
        $contractFactory = clone $blockchain->getContractFactory();
        $trackerIdentifier = self::TRACKER_CONTRACTIDS ;

        /** @var BlockchainContractFactory $contractFactory */

        $response= $this->getFactoryAndCreateEntities($foreignAdapter,BlockchainContractFactory::TOKENID,$contractFactory,$trackerIdentifier);

        $this->responseArray['contracts'] = $response ;

        return  $contractFactory;


    }

    public function getPopulatedBlockFactory(ForeignEntityAdapter $foreignAdapter){


        /** @var Blockchain $blockchain */
        $blockchain = $this->blockchain ;
        $blockFactory = clone $blockchain->getBlockFactory();
        $trackerIdentifier = self::TRACKER_BLOCKID ;
        $blockTime = self::TRACKER_BLOCKTIME ;

        /** @var BlockchainContractFactory $contractFactory */

        $blockList = array();

        foreach ($foreignAdapter->entityArray as $entity){


            $blockIndexToFind = $entity->get($trackerIdentifier) ;
            $entityToFindArray[] = $blockIndexToFind ;
            //each addressTracker might have multiple address

            $blockList[$blockIndexToFind]['blockIndex'] = $blockIndexToFind ;
            $blockList[$blockIndexToFind][self::TRACKER_BLOCKTIME] = $entity->get($blockTime)  ;
            $blockRawList[] = $blockIndexToFind ;

        }

        $countTotalEntities = count($blockList);

        $identifierUnid = $this->sandra->systemConcept->get($trackerIdentifier);
        $fileUnid = $this->sandra->systemConcept->get($blockFactory->entityIsa);

        //we search concepts with existing addresses
        $conceptsArray = DatabaseAdapter::searchConcept($blockRawList,$identifierUnid,$this->sandra,'',$fileUnid);

        //Missing preloading

        if(!empty($conceptsArray)) {
            $blockFactory->populateLocal();
        }
        else
            $blockFactory->populateLocal(1);

        //how many maching entities
        $matchingEntities = count($blockFactory->entityArray);

        $response['new'] = $countTotalEntities - $matchingEntities ;
        $response['existing'] =  $matchingEntities ;
        $response['total'] =  $countTotalEntities ;



        foreach ( $blockList as $blockData){

            /** @var BlockchainBlockFactory $blockFactory */

            $newBlock =  $blockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME,$blockData['blockIndex']);
            $newBlock->createOrUpdateRef('timestamp',$blockData[self::TRACKER_BLOCKTIME]);

        }



        $this->responseArray['blocks'] = $response ;

        return  $blockFactory;


    }

    public function getFactoryAndCreateEntities(ForeignEntityAdapter $foreignAdapter, $entityIdentifier,$entityFactory,$trackerIdentifier){


        /** @var BlockchainAddressFactory $entityFactory */

        $entityToFindArray = array();
        $stringListMap = array();


        foreach ($foreignAdapter->entityArray as $entity){


            $entityToFind = $entity->get($trackerIdentifier) ;
            $entityToFindArray[] = $entityToFind ;
            //each addressTracker might have multiple address
            foreach ($entityToFind as $entityIdentifierString){
                $stringListMap[$entityIdentifierString] = $entityIdentifierString ;
            }
        }

        //how many address in the map ?
        $countTotalEntities = count($stringListMap);

        //convert in numeric array
        $entityIdList = array_values($stringListMap);

        $identifierUnid = $this->sandra->systemConcept->get($entityIdentifier);
        $fileUnid = $this->sandra->systemConcept->get($entityFactory::$file);

        //we search concepts with existing addresses
        $conceptsArray = DatabaseAdapter::searchConcept($entityIdList,$identifierUnid,$this->sandra,'',$fileUnid);

        $entityFactory->conceptArray = $conceptsArray ;//we preload the factory with found concepts
        if(!empty($conceptsArray)) {
            $entityFactory->populateLocal();
        }

        //how many maching entities
        $matchingEntities = count($entityFactory->entityArray);

        $response['new'] = $countTotalEntities - $matchingEntities ;
        $response['existing'] =  $matchingEntities ;
        $response['total'] =  $countTotalEntities ;



        foreach ($entityIdList as $entityIdentifierString){

            $entityFactory->get($entityIdentifierString,true);
            //$foundEntity =$entityFactory->first($entityIdentifier,$entityIdentifierString);

            //if (is_null($foundEntity)){
            //  $entityFactory->getOrCreateFromRef('tokenId', $entityIdentifierString);

            //}



        }


        return  $response ;


    }

    public function getEvents($contract,$dataSource = 'default',$limit=null,$offset=null,$address = null){

        $dataSource = $this->getDataSource($dataSource);



        $foreignEntityEventsFactory = $dataSource->getEvents('default',$limit,$offset,$address);

        $structure = $foreignEntityEventsFactory->return2dArray();
        $totalResponses['structure'] = reset($structure);



        $blockFactory = $this->getPopulatedBlockFactory($foreignEntityEventsFactory);
        // die();

        $addressFactory = $this->getPopulatedAddressFactory($foreignEntityEventsFactory);
        $contractFactory = $this->getPopulatedContractFactory($foreignEntityEventsFactory);



        $this->saveEvents($foreignEntityEventsFactory,$this->blockchain,$contractFactory,$addressFactory,$blockFactory);


        // $newAddress = count($addressFactory->newEntities);

        $totalResponses['data'] = $this->responseArray ;
        return $totalResponses ;



    }

}