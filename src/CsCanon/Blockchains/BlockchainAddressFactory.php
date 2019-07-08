<?php

namespace CsCanon\Blockchains;

use CsCanon\BlockchainRouting;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

abstract class BlockchainAddressFactory extends EntityFactory
{
   public $blockchain ;
    public static $isa ;
    public static $file ;
    public $foreignAdapterX ;
    protected static $className = 'App\XcpAddress' ;
    const ADDRESS_SHORTNAME = 'address' ;



   public function __construct(){

     parent::__construct(static::$isa,static::$file,app('Sandra')->getSandra());



     $this->generatedEntityClass = static::$className ;

     $foreignAdapter = new ForeignEntityAdapter('http://www.google.com','',app('Sandra')->getSandra());
     $this->foreignAdapterX = $foreignAdapter ;

   }

    public function get($address,$autoCreate = false){

        $addressName = self::ADDRESS_SHORTNAME;
        $entity = $this->first($addressName,$address);


       if(is_null($entity) && !$autoCreate){
           $refConceptId = CommonFunctions::somethingToConceptId(static::$isa,app('Sandra')->getSandra());
           $entity = new static::$className("foreign$address",array($addressName => $address),$this->foreignAdapterX,$this->entityReferenceContainer, $this->entityContainedIn, "foreign$address",$this->system);
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
