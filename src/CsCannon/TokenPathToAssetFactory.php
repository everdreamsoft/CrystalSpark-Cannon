<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use SandraCore\DatabaseAdapter;
use SandraCore\EntityFactory;
use SandraCore\System;

/** No use for now */

class TokenPathToAssetFactory extends EntityFactory
{

    protected static $isa = 'tokenPath' ;
    protected static $file = 'tokenPathFile' ;
    const ID = 'code';

    public function __construct(System $sandra){

        parent::__construct(static::$isa,static::$file,$sandra);


    }

    public function get($identifierString){


       return  $this->last(self::ID,$identifierString);

    }


    public function getOrCreate(BlockchainContractStandard $standard){

        $result = $this->get($standard->getDisplayStructure());
        if ($result == null) $result = $this->create($standard);

        return $result ;

    }


    public function getOrbsInCollection(AssetCollection $assetCollection,$limit,$offset){




    }

    public function create(BlockchainContractStandard $specifier){

        $sandra = SandraManager::getSandra();

        $specifier->verifyTokenPath($specifier->getSpecifierData());

        //check if doens't exist in db
        $displayStructure = $specifier->getDisplayStructure();

        $conceptForSearch = new TokenPathToAssetFactory($sandra);
        $result = $conceptForSearch->getOrCreateFromRef($this::ID,$displayStructure);
        $this->addNewEtities([$result->subjectConcept->idConcept=>$result],$conceptForSearch->sandraReferenceMap);

        return $result;





    }



}