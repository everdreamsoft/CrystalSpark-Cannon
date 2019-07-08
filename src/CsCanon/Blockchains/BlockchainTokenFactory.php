<?php

namespace CsCanon\Blockchains;

use CsCanon\AssetFactory;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

abstract class BlockchainTokenFactory extends EntityFactory
{


    public $blockchain ;
    protected static $isa ;
    protected static $file ;
    protected $foreignAdapterX ;
    protected static $className = '' ;
    public static $joinAssetVerb = 'bindAsset';
    public static $mainIdentifier = 'tokenId';


   public function __construct(){

     parent::__construct(static::$isa,static::$file,app('Sandra')->getSandra());

     $this->generatedEntityClass = static::$className ;



   }

    public function joinAsset (AssetFactory $factory){

        $this->joinFactory(BlockchainTokenFactory::$joinAssetVerb,$factory);


    }

    public function create($tokenId){

      return $this->createNew(array(self::$mainIdentifier=>$tokenId));


    }

    public function getOrCreate($tokenId){

       if(!is_null($this->first(self::$mainIdentifier,$tokenId)))
           return $this->first(self::$mainIdentifier,$tokenId) ;

        return $this->createNew(array(self::$mainIdentifier=>$tokenId));


    }








}
