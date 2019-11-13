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

abstract class AssetSolver extends Entity
{
    const ISA = 'assetSolver';
    const FILE = 'assetSolverFile';

    const LAST_UPDATE_SHORTNAME = 'updateTimestamp';
    public static $lastUpdate = null ;
    public static $solverEntity = null ;
    public  $additionalSolverParam = null ;


public abstract static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract):?array ;
protected abstract static function updateSolver() ;



public static function loadContractsAssets(BlockchainContractFactory $contractFactory){



}

public static function update($onlyIfOlderThanSec = null){



    static::updateSolver();
    $now = mktime();


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

    public  function setAdditionalParam($array){

        $this->additionalSolverParam = $array ;



    }


    public static function getEntity():self {

    if( is_null(self::$solverEntity)) {

        $solverFactory = new MetadataSolverFactory(SandraManager::getSandra());
        $solverFactory->populateLocal();
        self::$solverEntity = $solverFactory->getOrCreateFromRef('class_name', static::class);

    }

    return self::$solverEntity;



    }

}