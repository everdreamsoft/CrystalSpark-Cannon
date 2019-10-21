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
use SandraCore\Reference;

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

            foreach ($this->specificatorArray ? $this->specificatorArray :array() as $key => $value) {



                $pathItemUnid = $this->system->systemConcept->get($value);



                if( !isset($tokenPath[$value]) and !isset($tokenPath[$pathItemUnid])) {
                    throw new \Exception("" .$this->getStandardName() ." token require $value for contract");



                }

                if (isset($tokenPath[$pathItemUnid])) $pathValue = $tokenPath[$pathItemUnid] ;
                if (isset($tokenPath[$value])) $pathValue = $tokenPath[$value] ;

                if ($pathValue instanceof Reference){
                    $pathValue = $pathValue->refValue;
                }


                $this->specificatorData[$value] = $pathValue ;

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

            $localEntity =  static::$entityClassArray[static::class];

        }

        return $localEntity;

    }

    public static function init()
    {

        return static::getEntity();

    }




}