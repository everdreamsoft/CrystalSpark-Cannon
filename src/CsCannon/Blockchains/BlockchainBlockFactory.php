<?php

namespace CsCannon\Blockchains;


use CsCannon\BlockchainRouting;

use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

class BlockchainBlockFactory extends EntityFactory
{
   public $blockchain ;
    public  $isa ;
    public  $file  ;
    public $foreignAdapterX ;
    protected static $className = 'CsCannon\Blockchains\BlockchainBlock' ;
    const  INDEX_SHORTNAME = 'blockIndex';
    const  BLOCK_TIMESTAMP = 'timestamp';



   public function __construct(Blockchain $blockchain){

       $blockIsa = $blockchain::$blockchainConceptName.'Bloc' ;
       $this->file = $blockchain::$blockchainConceptName.'BlocFile' ;
       $this->entityIsa = $blockIsa ;




     parent::__construct($blockIsa,$this->file,SandraManager::getSandra());



     $this->generatedEntityClass = static::$className ;



   }

    public function get($id):BlockchainBlock
    {
        return $this->first(self::INDEX_SHORTNAME,$id) ;
    }




}
