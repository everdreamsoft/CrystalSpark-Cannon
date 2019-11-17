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
use SandraCore\System;

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

        return $this ;


    }

    public function setTokenPath($tokenPath){

        return $this->verifyTokenPath($tokenPath);

    }

    public function getSpecifierData(){

        return $this->specificatorData ;

    }

    public static function getEntity($data=null):self
    {

        $sandra = SandraManager::getSandra();

        $localEntity = null ;
        $standardFactory = BlockchainStandardFactory::getStatic(SandraManager::getSandra());

        if ($sandra->entityToClassStore(static::class,$standardFactory))
            $localEntity = $sandra->entityToClassStore(static::class,$standardFactory) ;
        /** @var BlockchainContractStandard $localEntity */


        if (is_null($localEntity) or $localEntity->system->instanceId != SandraManager::getSandra()->instanceId) {




            static::$entityClassArray[static::class] = $standardFactory->getOrCreateFromRef('class_name', static::class);

            $localEntity =  static::$entityClassArray[static::class];

        }

        if($data) $localEntity->setTokenPath($data);
        $newEntity = clone $localEntity ;

        return $newEntity;

    }

    public static function init($data = null)
    {

        return static::getEntity($data);

    }

    public static function getJsonFromStandardArray($array)
    {

        $csv = '';
        $totalArray = array();

        foreach ($array as $standard){
            /** @var \CsCannon\Blockchains\BlockchainContractStandard $standard */
            $standard->specificatorData['_class_'] = get_class($standard);
            $totalArray[] = $standard->specificatorData ;


        }

        $json = json_encode($totalArray);

        return $json ;


    }

    public static function getStandardsFromJson($json)
    {

        $array = json_decode($json,1);
        $standard = [];

        foreach ($array as $path){

            $class = $path['_class_'];

            $standard[] = $class::init($path);

        }

        return $standard;


    }




}