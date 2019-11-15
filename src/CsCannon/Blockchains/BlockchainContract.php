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
use CsCannon\AssetFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Displayable;
use CsCannon\DisplayManager;
use SandraCore\Entity;
use SandraCore\System;

 abstract class  BlockchainContract extends Entity Implements Displayable
{

    abstract  function getBlockchain():Blockchain;
    public $id ;
    public $displayManager ;

    const DISPLAY_ID = 'contract';

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system){

        /** @var System $system */

        if (!isset($sandraReferencesArray[BlockchainContractFactory::MAIN_IDENTIFIER]) &&
            !isset($sandraReferencesArray[$system->systemConcept->get( BlockchainContractFactory::MAIN_IDENTIFIER)])){
            $system->systemError(1,self::class,3,"contract must have an id");

        }



        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->id = $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);


    }

    public function getStandard():?BlockchainContractStandard{



        $return = $this->getJoinedEntities(BlockchainContractFactory::CONTRACT_STANDARD);
        if (is_array($return)) $return = end($return);

        return $return ;


    }

    public function setStandard(BlockchainContractStandard $standard){

        $this->setBrotherEntity(BlockchainContractFactory::CONTRACT_STANDARD,$standard,null);

        return $this ;


    }

    public function getCollections(){

        $collectionEntities = null ;
        $this->factory->getTriplets();
        $entitiesArray = $this->getJoinedEntities(BlockchainContractFactory::JOIN_COLLECTION);
        //we keep only collections entities
        foreach($entitiesArray ? $entitiesArray : array() as $entity){
            if ($entity instanceof AssetCollection) $collectionEntities[] = $entity ;

        }

        return $collectionEntities;





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

    public function returnArray($displayManager)
    {
        $return = $this->getId();


        //In case we specified a specifier for the contract like tokenId = 1
        if ($displayManager->dataStore['specifier']){

            $return = array();

            $token = $displayManager->dataStore['specifier'];

            /** @var BlockchainContractStandard $token */

            $return['address'] = $this->getId();
            $return['standard'] = $token->getStandardName() ;

            $return['token'] = $token->specificatorData ;

        }




        return $return ;
    }

    public function display($specifier = null): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        $this->displayManager->dataStore['specifier'] = $specifier;

        return $this->displayManager ;
    }



 }