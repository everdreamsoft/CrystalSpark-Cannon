<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;






use CsCannon\Asset;
use CsCannon\AssetCollection;
use SandraCore\Entity;
use SandraCore\System;

abstract class  BlockchainContract extends Entity
{

    abstract  function getBlockchain():Blockchain;
    public $id ;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system){

        /** @var System $system */

        if (!isset($sandraReferencesArray[BlockchainContractFactory::MAIN_IDENTIFIER]) &&
            !isset($sandraReferencesArray[$system->systemConcept->get( BlockchainContractFactory::MAIN_IDENTIFIER)])){
        $system->systemError(1,self::class,3,"contract must have an id");

        }



        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

         $this->id = $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);


    }

    public function getCollections(){


        return $this->getJoinedEntities(BlockchainContractFactory::JOIN_COLLECTION);



    }

    public function bindToAsset(Asset $asset){

        $this->setBrotherEntity(BlockchainContractFactory::JOIN_ASSET,$asset,null);


    }

    public function bindToCollection(AssetCollection $collection){

        $this->setBrotherEntity(BlockchainContractFactory::JOIN_COLLECTION,$collection,null);


    }



    public function getId(){


        return $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);


    }










}