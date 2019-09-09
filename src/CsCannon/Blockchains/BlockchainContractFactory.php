<?php

namespace CsCannon\Blockchains;

use CsCannon\Blockchains\BlockchainAddressFactory;

use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

class BlockchainContractFactory extends EntityFactory
{

const TOKENID = 'tokenId';
protected static $file = 'blockchainContractFile';
protected static $isa = null;
protected static $className = 'CsCannon\BlockchainContract';

    public function __construct(){

        parent::__construct(static::$isa,static::$file,SandraManager::getSandra());



        $this->generatedEntityClass = static::$className ;



    }

public function resolveMetaData (){


    return 'helloMeta';

}







}
