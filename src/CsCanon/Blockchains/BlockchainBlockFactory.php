<?php

namespace CsCanon\Blockchains;

use CsCanon\Blockchain;
use CsCanon\BlockchainRouting;

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
    protected static $className = 'App\Blockchains\BlockchainBlock' ;
    const  INDEX_SHORTNAME = 'blockIndex';






   public function __construct(Blockchain $blockchain){

       $blockIsa = $blockchain::$blockchainConceptName.'Bloc' ;
       $this->file = $blockchain::$blockchainConceptName.'BlocFile' ;
       $this->entityIsa = $blockIsa ;




     parent::__construct($blockIsa,$this->file,app('Sandra')->getSandra());



     $this->generatedEntityClass = static::$className ;



   }

    public function get($id)
    {
        return $this->first(self::INDEX_SHORTNAME,$id) ;
    }




}
