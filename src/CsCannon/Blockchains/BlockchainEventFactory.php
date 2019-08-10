<?php

namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\SandraManager;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

 class BlockchainEventFactory extends EntityFactory
{
    public $blockchain ;
    public static $isa = 'blockchainEvent' ;
    public static $file = 'blockchainEventFile';

    protected static $className = 'CsCannon\Blockchains\BlockchainEvent' ;

    public  $requirementAbstractArray = array();
    public  $requirementAbstractTripletArray = array();
    const EVENT_TYPE = 'eventType';
    const EVENT_SOURCE_ADDRESS = 'source';
    const EVENT_DESTINATION_VERB = 'hasSingleDestination';
     const EVENT_CONTRACT = 'blockchainContract';
     const ON_BLOCKCHAIN_EVENT = 'onBlockchain';
    public static $messagePool = array();


    private $genericAddressFactory = EntityFactory::class ;

    const EVENT_TRANSFER = 'transfer';

    public $contractAddress = '';



   public function __construct(){

     parent::__construct(static::$isa,static::$file,SandraManager::getSandra());



     $this->generatedEntityClass = static::$className ;


   }


    public function getRequired(){

         $this->requirementAbstractArray = array(Blockchain::$blockchainConceptName,
             'timestamp',
             Blockchain::$txidConceptName,

          );

         return $this->requirementAbstractArray ;

    }

     public function filterBySender($senderEntity){

        $this->setFilter(BlockchainEventFactory::EVENT_SOURCE_ADDRESS,$senderEntity);

         return $this ;

     }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC')
    {



        $return = parent::populateLocal($limit, $offset, $asc);

        $this->getTriplets();



        return $return ;
    }

     public function getRequiredTriplets(){

        $this->requirementAbstractTripletArray = array(
            self::EVENT_SOURCE_ADDRESS,
            self::EVENT_DESTINATION_VERB,
           self::EVENT_CONTRACT,
            self::EVENT_TYPE,
           );

        return $this->requirementAbstractTripletArray ;

    }

     public function create(Blockchain $blockchain,
                            BlockchainAddress $sourceAddressConcept,
                            BlockchainAddress $destinationAddressConcept,
                            Entity $contract,
                            $txid,
                            $timestamp,
                            BlockchainBlock $block,
                            $tokenId = null,
                            $quantity = 1

 )
     {

         $dataArray[Blockchain::$txidConceptName] = $txid ;
         $dataArray[Blockchain::$txidConceptName] = $quantity ;
         $dataArray['timestamp'] = $timestamp ;




         /** @var BlockchainContractFactory $contractFactory */

         $triplets[self::EVENT_SOURCE_ADDRESS] = $sourceAddressConcept ;
         $triplets[self::EVENT_DESTINATION_VERB] = $destinationAddressConcept ;

         $triplets[self::ON_BLOCKCHAIN_EVENT] = $blockchain::NAME ;

         //does the contract has a token id ?
         if (!is_null($tokenId)){
             $triplets[self::EVENT_CONTRACT] = array($contract->subjectConcept->idConcept=>array(BlockchainContractFactory::TOKENID=>$tokenId));

         }

         else {
             $triplets[self::EVENT_CONTRACT] = $contract;

         }


         return parent::createNew($dataArray, $triplets);
     }

   public function legacyToDelete($dataArray, $linArray = null)
   {

       /* legacy */


      if( !$this->localVerifyIntegrity($dataArray, $linArray)) return null ;


        $blockchain = $linArray['blockchain'] ;
        /** @var Blockchain $blockchain */



      //address
       $addressFactory = BlockchainRouting::getAddressFactory($linArray[BlockchainEventFactory::EVENT_SOURCE_ADDRESS]);
       $contractFactory = $blockchain->getContractFactory();

       $tokenId = $dataArray['tokenId'];

       /** @var BlockchainAddressFactory $addressFactory */

       $address = $addressFactory->get($linArray[BlockchainEventFactory::EVENT_SOURCE_ADDRESS],true);
       $addressDestination = $addressFactory->get($linArray[BlockchainEventFactory::EVENT_DESTINATION_VERB],true);
       if (is_null($contractFactory)) {
         return ;
       }


       /** @var BlockchainContractFactory $contractFactory */
       $contract = $contractFactory->get($linArray[BlockchainEventFactory::EVENT_CONTRACT], true);
       $triplets[self::EVENT_SOURCE_ADDRESS] = $address ;
       $triplets[self::EVENT_DESTINATION_VERB] = $addressDestination ;
       $triplets[self::EVENT_CONTRACT] = array($contract->subjectConcept->idConcept=>array(BlockchainContractFactory::TOKENID=>$tokenId));
       $triplets[self::ON_BLOCKCHAIN_EVENT] = $dataArray[Blockchain::$blockchainConceptName];


       return parent::createNew($dataArray, $triplets);
   }

    public function localVerifyIntegrity($dataArray,$linkArray)
    {

        $sandra= SandraManager::getSandra();
        /** @var System $sandra */


        //First we check if all required parameters are present (for all events)
        foreach ($this->getRequired() as $required){

        //test if ref exists
            if (!isset($dataArray[$required]) and !isset($dataArray[$sandra->systemConcept->get($required)])){

                self::$messagePool[] = "$required not defined for transaction";
                return false ;
            }


        }
        //if it is a transfer we require a source and a destination

        foreach ($this->getRequiredTriplets() as   $requiredVerb){

            //test if ref exists
            if (!isset($linkArray[$requiredVerb]) and !isset($dataArray[$sandra->systemConcept->get($requiredVerb)])){

                self::$messagePool[] = "$requiredVerb not defined for transaction";
                return false ;
            }


        }

        //we make a check for specific events
        if ($linkArray[BlockchainEventFactory::EVENT_TYPE] == BlockchainEventFactory::EVENT_TRANSFER){
            if(!$this->verifyTransfer($dataArray,$linkArray)) return false ;


        }


        return true ;


    }

     private function verifyTransfer($dataArray,$linkArray){


        if ($linkArray[BlockchainEventFactory::EVENT_SOURCE_ADDRESS] == $linkArray[BlockchainEventFactory::EVENT_DESTINATION_VERB]){

            //dd("we have a birth");
            return false ;
        }

        return true ;

     }


    public function buildAddressFactory(){

       foreach ($this->entityArray as $eventEntity){

           /** @var BlockchainEvent $eventEntity */

           $source = $eventEntity->getSourceAddress();

       }


    }

     public function getArray(){


         foreach ($this->entityArray as $eventEntity){

             $contractAdress = null ;

             /** @var BlockchainEvent $eventEntity */
             $source = $eventEntity->getSourceAddress();
             try {
                 $contract = $eventEntity->getBlockchainContract();
                 if($contract instanceof BlockchainContract or $contract instanceof BlockchainToken) {
                     $contractAdress = $contract->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);
                     $eventData['asset'] = $contract->resolveMetaData($eventEntity->getTokenId());
                 }




             }
             catch (\Exception $e){
                 /** @var BlockchainContract $contract */

                 $contractAdress = 'null' ;


                 //  continue ;
             }

             /** @var BlockchainContract $contract */


             $timestamp = $eventEntity->get('timestamp');
             $arrayKey = $timestamp.'.'.$eventEntity->get(Blockchain::$txidConceptName);

             $eventData['tokenId'] =  $eventEntity->getTokenId();
             // $eventData['opensea'] =  $eventEntity->get('openSeaId');
             $eventData['source'] =  $source ;
             $eventData['destination'] =   $eventEntity->getDestinationAddress();
             $eventData['quantity'] =   $eventEntity->get('quantity');
             $eventData['timestamp'] =   $eventEntity->get('timestamp');

             $eventData['contract'] =   $contractAdress;


             $eventData['txHash'] =$eventEntity->get(Blockchain::$txidConceptName);
             //$eventData['tokenData'] = $tokenData ;

             //$joinedAssets = $contract->get

             $returnArray[$arrayKey] =  $eventData ;

             $contract = null ;

         }


         return $returnArray ;



     }




}

