<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 17:07
 */

namespace CsCannon\Blockchains;


use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\BlockchainStandardFactory;
use CsCannon\MetadataSolverFactory;
use CsCannon\Orb;
use CsCannon\SandraManager;
use SandraCore\Entity;

abstract class BlockchainContractStandard extends Entity
{


    public $specificatorArray = array();
    public $specificatorData = array();
    public abstract function resolveAsset(Orb $orb) ;
    public abstract function getStandardName() ;
    public abstract function getDisplayStructure() ;
    public static $entityClassArray = null ;




    public function verifyTokenPath($tokenPath){

        try {

            foreach ($this->specificatorArray as $key => $value) {

                if( !isset($tokenPath[$value])) {
                    throw new \Exception("" .$this->getStandardName() ." token require $value for contract");



                }

                $this->specificatorData[$value] = $tokenPath[$value] ;

            }
        }
        catch (\Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            die();
        }


    }

    public function setTokenPath($tokenPath){

        $this->verifyTokenPath($tokenPath);

    }

    public function getSpecifierData(){

        return $this->specificatorData ;

    }

    public static function getEntity():self
    {

        $localEntity = null ;

        if (isset(static::$entityClassArray[static::class]))
        $localEntity = static::$entityClassArray[static::class] ;
        /** @var BlockchainContractStandard $localEntity */


        if (is_null($localEntity) or $localEntity->system->instanceId != SandraManager::getSandra()->instanceId) {

            $standardFactory = BlockchainStandardFactory::getStatic(SandraManager::getSandra());


            static::$entityClassArray[static::class] = $standardFactory->getOrCreateFromRef('class_name', static::class);
            $standardFactory->createViewTable("temporay");
            $localEntity =  static::$entityClassArray[static::class];

        }

        return $localEntity;

    }

    public static function init()
    {

        return static::getEntity();

    }




}