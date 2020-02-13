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

     parent::__construct(static::$isa,static::$file,SandraManager::getSandra());



     $this->generatedEntityClass = static::$className ;

     $foreignAdapter = new ForeignEntityAdapter(null,'',SandraManager::getSandra());
     $this->foreignAdapterX = $foreignAdapter ;

   }

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

            $entity = $this->createNew(array(self::ADDRESS_SHORTNAME=>$address));

            //dd($entity);

        }


       $entity->setAddress($address);


       return $entity ;

    }


}
