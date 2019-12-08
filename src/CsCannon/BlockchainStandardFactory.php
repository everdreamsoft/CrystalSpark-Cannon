<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 10/10/2019
 * Time: 18:23
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContractStandard;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

class BlockchainStandardFactory extends EntityFactory
{

    protected static $isa = 'blockchainStandard' ;
    protected static $file = 'blockchainStandardFile' ;
    private static $blockchainStandardFactory ; /** @var BlockchainContractStandard  */

    /**
     * @param System $sandra
     */
    public function __construct(System $sandra){

        parent::__construct(static::$isa,static::$file,$sandra);


    }

    public static function getStatic(System $sandra){

        if (self::$blockchainStandardFactory == null or !isset(self::$blockchainStandardFactory->system) or
            self::$blockchainStandardFactory->system->instanceId != $sandra->instanceId){
            self::$blockchainStandardFactory = new BlockchainStandardFactory($sandra);
            self::$blockchainStandardFactory->populateLocal();


        }

        return   self::$blockchainStandardFactory ;


    }



}