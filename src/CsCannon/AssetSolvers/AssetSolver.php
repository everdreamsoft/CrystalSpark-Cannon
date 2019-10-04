<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 11:04
 */

namespace CsCannon\AssetSolvers;


use CsCannon\AssetCollection;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Orb;
use CsCannon\SandraManager;
use SandraCore\Entity;
use SandraCore\EntityFactory;

abstract class AssetSolver
{
    const ISA = 'assetSolver';
    const FILE = 'assetSolverFile';
    const INDEX = 'classIndex';
    const LAST_UPDATE_SHORTNAME = 'updateTimestamp';
    public static $lastUpdate = null ;
    public static $solverEntity = null ;


public abstract static function resolveAsset(Orb $orb, BlockchainContractStandard $specifier) ;
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

    public static function getEntity():Entity{

    if( is_null(self::$solverEntity)) {

        $solverFactory = new EntityFactory(self::ISA, self::FILE, SandraManager::getSandra());
        $solverFactory->populateLocal();
        self::$solverEntity = $solverFactory->getOrCreateFromRef(self::INDEX, self::class);

    }

    return self::$solverEntity;



    }

}