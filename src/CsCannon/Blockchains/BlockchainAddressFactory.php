<?php

namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

abstract class BlockchainAddressFactory extends EntityFactory
{
   public $blockchain ;
    public static $isa ;
    public static $file = 'blockchainAddressFile' ;
    public $foreignAdapterX ;
    protected static $className = 'CsCannon\XcpAddress' ;
    const ADDRESS_SHORTNAME = 'address' ;
    public $balance = null ;



   public function __construct(){


      //issue on constructor
     parent::__construct(static::$isa,static::$file,SandraManager::getSandra());



     $this->generatedEntityClass = static::$className ;

     $foreignAdapter = new ForeignEntityAdapter(null,'',SandraManager::getSandra());
     $this->foreignAdapterX = $foreignAdapter ;

     $this->blockchain = static::getBlockchain();

   }

    public static function getAddress($address,$autoCreate = false):BlockchainAddress{

        $factoryOfSelf = new static();

        return $factoryOfSelf->get($address,$autoCreate);


    }
    public abstract static function getBlockchain();

    public function get($address,$autoCreate = false):BlockchainAddress{

        $addressName = self::ADDRESS_SHORTNAME;
        $entity = $this->first($addressName,$address);


       if(is_null($entity) && !$autoCreate){
           $isa = static::$isa ;
           if (!$isa) $isa= 'genericBlockchain';
           $refConceptId = CommonFunctions::somethingToConceptId($isa,SandraManager::getSandra());
           $entity = new static::$className("foreign$address",array($addressName => $address),$this->foreignAdapterX,"f:$address",$this->entityReferenceContainer, $this->entityContainedIn,$this->system);
           $this->addNewEtities($entity,array($refConceptId=>$entity));

           //dd($entity);

       }
        if(is_null($entity) && $autoCreate){

           if(empty($address)){
               dd("empty address");

           }

            $additionalBrothers = [
                BlockchainContractFactory::ON_BLOCKCHAIN_VERB => [$this->blockchain::NAME => ['creationTimestamp'=>time()]]
            ];

            $entity = $this->createNew(array(self::ADDRESS_SHORTNAME=>$address),$additionalBrothers);



        }


       $entity->setAddress($address);


       return $entity ;

    }




}
