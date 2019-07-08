<?php

namespace CsCanon\Blockchains;

use CsCanon\BlockchainRouting;
use CsCanon\Blockchains\Counterparty\XcpAddressFactory;
use CsCanon\Blockchains\Ethereum\EthereumContractFactory;
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

    protected static $className = 'CsCanon\Blockchains\BlockchainEvent' ;

    public  $requirementAbstractArray = array();
    public  $requirementAbstractTripletArray = array();
    const EVENT_TYPE = 'eventType';
    const EVENT_SOURCE_ADDRESS = 'source';
    const EVENT_DESTINATION_VERB = 'hasSingleDestination';
     const EVENT_CONTRACT = 'blockchainContract';
     const ON_BLOCKCHAIN_EVENT = 'onBlockchain';
   // const EVENT_SINGLE_DESTINATION_TARGET = 'singleDestinationAddress';
    public static $messagePool = array();

    public $sourceAddress = array();
    public $destinationAddress = array();
    private $genericAddressFactory = EntityFactory::class ;

    const EVENT_TRANSFER = 'transfer';

    public $contractAddress = '';



   public function __construct(){

     parent::__construct(static::$isa,static::$file,app('Sandra')->getSandra());



     $this->generatedEntityClass = static::$className ;


   }


    public function getRequired(){

         $this->requirementAbstractArray = array(Blockchain::$blockchainConceptName,
             'timestamp',
             Blockchain::$txidConceptName,

          );

         return $this->requirementAbstractArray ;

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
                            $tokenId = null

 )
     {

         $dataArray[Blockchain::$txidConceptName] = $txid ;
         $dataArray['timestamp'] = $timestamp ;





         //if( !$this->localVerifyIntegrity($dataArray, $linArray)) return null ;


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

   public function createNew($dataArray, $linArray = null)
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
         dd($contractFactory);
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

        $sandra= app('Sandra')->getSandra();
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
           dd($source);
       }


    }


}
