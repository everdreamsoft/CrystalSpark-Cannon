<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 11:04
 */

namespace CsCannon\AssetSolvers;


use CsCannon\AssetCollection;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\MetadataSolverFactory;
use CsCannon\Orb;
use CsCannon\SandraManager;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

abstract class AssetSolver extends Entity
{
    const ISA = 'assetSolver';
    const FILE = 'assetSolverFile';

    const LAST_UPDATE_SHORTNAME = 'updateTimestamp';
    const IDENTIFIER = 'identifier';
    public static $lastUpdate = null ;
    public static $solverEntity = null ;
    public $additionalSolverParam ;
    //public $paramInfo = ['url'=>'required','somethingNotRequired'=>'notRequired']; //child solver should specify required parameters if any
    public $paramInfo = [];
    public $valid = null;




public abstract static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract):?array ;
protected abstract static function updateSolver() ;



public static function loadContractsAssets(BlockchainContractFactory $contractFactory){}

public abstract static function getSolverIdentifier();


public static function update($onlyIfOlderThanSec = null){



    static::updateSolver();
    $now = time();


    $entity = self::getEntity();
    $entity->createOrUpdateRef(self::LAST_UPDATE_SHORTNAME,$now);
    self::$solverEntity = $entity;




}

    public static function getLastUpdate(){

        $entity = self::getEntity();
       return $entity->get(self::LAST_UPDATE_SHORTNAME);


    }

    public  function getAdditionalParam(){

        return $this->additionalSolverParam ;



    }

    /**
     * Verify if the solver needs specific parameters and remove unessesary ones
     * @param $params array
     * @return bool
     */
    public  function filterAndSetParam($params){

        $requiredParam = $this->paramInfo;
        $filteredParam = [];

        if (!empty($requiredParam)){

            foreach ($this->paramInfo as $keyParam => $requiredString){

                if (isset($params[$keyParam])){
                    $filteredParam[$keyParam] = $params[$keyParam];
                }
                else if ($requiredString == 'required'){
                    //we are missing a required param
                    SandraManager::getSandra()->systemError('11',
                        'AssetSolver',2,
                        'AssetSolver '.static::class." is missing $keyParam");
                    $this->valid = false ;
                    return false ;
                }


            }

        }

        $this->setAdditionalParam($filteredParam);
        return true ;


    }

    public function validate(){

        $requiredParam = $this->paramInfo;
        $this->valid = true ;
        $params = $this->additionalSolverParam;

        if (!empty($requiredParam)){

            foreach ($this->paramInfo as $keyParam => $requiredString){

                if (!isset($params[$keyParam])&& $requiredString == 'required'){
                    $this->valid = false ;
                    return false ;
                }
                }
        }

        return true ;

    }

    public  function setAdditionalParam($array){

        $this->additionalSolverParam = $array ;

    }


    public static function getEntity():self {

       $sandra = SandraManager::getSandra();


        $solverFactory = new MetadataSolverFactory($sandra);
        $solverEntity = $sandra->entityToClassStore(static::class,$solverFactory);
        /** @var Entity $solverEntity */
        $solverEntity->getOrInitReference(self::IDENTIFIER,static::getSolverIdentifier());


    return $sandra->entityToClassStore(static::class,$solverFactory);



    }

}
