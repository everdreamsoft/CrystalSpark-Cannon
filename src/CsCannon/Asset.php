<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

class Asset extends \SandraCore\Entity implements Displayable
{

    public $metaDataUrl;
    public $imageUrl;
    public $id ;
    public $fallbackImage ;


    public const IMAGE_URL = 'imgURL';
    public const METADATA_URL = 'metadataUrl';
    public const FALLBACK_IMAGE = 'fallbackImageUrl';
    public $tokenPathFactory ;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {

        $this->tokenPathFactory = new TokenPathToAssetFactory($system);


        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
        //if the factory is foreign we map data to correct concepts ids

        if ($factory instanceof ForeignEntityAdapter){

            $this->imageUrl = $sandraReferencesArray[AssetFactory::IMAGE_URL];
            $this->id = $sandraReferencesArray[AssetFactory::ID];
            $this->metaDataUrl = $sandraReferencesArray[AssetFactory::METADATA_URL];

            if (isset ($sandraReferencesArray[Asset::FALLBACK_IMAGE]))
                $this->fallbackImage = $sandraReferencesArray[Asset::FALLBACK_IMAGE];

        }
        else{

            $this->imageUrl = $this->get(AssetFactory::IMAGE_URL);
            $this->id = $this->get(AssetFactory::ID);
            $this->metaDataUrl =  $this->get(AssetFactory::METADATA_URL);
            $this->fallbackImage = $this->get(Asset::FALLBACK_IMAGE);



        }




    }

    public $displayable = array(
        'id'=>'id',
        'image'=>'image',


    );

    public function bindToContract(BlockchainContract $contract,$replaceExisting = false){


        $this->setBrotherEntity(AssetFactory::$tokenJoinVerb,$contract,null,true,$replaceExisting);

    }

    public function bindToCollection(AssetCollection $collection, $replaceExisting = false){


        $this->setBrotherEntity(AssetFactory::$collectionJoinVerb,$collection,null,true,$replaceExisting);

    }

    public function setImageUrl($url){


        $this->createOrUpdateRef(Asset::IMAGE_URL,$url);
        $this->imageUrl = $url ;

    }

    public function setMetadataUrl($url){


        $this->createOrUpdateRef(Asset::METADATA_URL,$url);
        $this->metaDataUrl = $url ;

    }

    /**
     * @param BlockchainContract $contract
     * @param Entity[] $tokenPaths array of token path
     */
    public function bindToContractWithMultipleSpecifiers(BlockchainContract $contract, array $tokenPaths){


        foreach ($tokenPaths ?? array() as $tokenPath){

            //System::sandraException(SandraManager::getSandra()->systemError("",self::class,3,"Binding asset with contract specifier not a valid blockchain standard
            //"));
            //continue ;

            $contract->setExplicitTokenId(1);
            $tokenPath->subjectConcept->createTriplet($contract->subjectConcept,$this->subjectConcept);
            // $this->subjectConcept->createTriplet($contract->subjectConcept,$tokenPath->subjectConcept);


        }


    }


    public function getContracts(){



        $contracts = null ;
        $this->factory->getTriplets();
        $entitiesArray = $this->getJoinedEntities(AssetFactory::$tokenJoinVerb);
        //we keep only contract entities
        foreach($entitiesArray ? $entitiesArray : array() as $entity){
            if ($entity instanceof BlockchainContract) $contracts[] = $entity ;

        }

        //$this->factory->populateBrotherEntities()
        return $contracts;

    }

    public function getTokenPathForContract(BlockchainContract $contract, $limit=1000){

        //tokenpath->contract->asset
        $this->tokenPathFactory->setFilter($contract->subjectConcept->idConcept,$this->subjectConcept->idConcept);
        $this->tokenPathFactory->populateLocal($limit);

        return $this->tokenPathFactory->getEntities();



    }


    public function getCollections(){

        $collectionEntities = null ;
        $this->factory->getTriplets();
        $entitiesArray = $this->getJoinedEntities(AssetFactory::$collectionJoinVerb);
        //we keep only collections entities
        foreach($entitiesArray ? $entitiesArray : array() as $entity){
            if ($entity instanceof AssetCollection) $collectionEntities[] = $entity ;

        }

        return $collectionEntities;

    }





    public function getDisplayable(){

        foreach ($this->displayable as $referenceShortname => $referenceTitle){

            $return[$referenceTitle] = $this->get($referenceShortname) ;
        }

        return $return ;

    }

    public function getDisplayableCollection($collectionEntityArray, $simple = 'false'){

        foreach ($this->displayable as $referenceShortname => $referenceTitle){

            $return[$referenceTitle] = $this->get($referenceShortname) ;
        }

        if (is_array($collectionEntityArray))
            foreach ($collectionEntityArray as $collectionEntity){

                $return['collections'][] = $collectionEntity->getDefaultDisplay($simple);

            }



        return $return ;

    }


    public function returnArray(\CsCannon\DisplayManager $display)
    {

        $assetData[self::IMAGE_URL] = $this->imageUrl ;
        if(isset ($this->fallbackImage))
            $assetData[self::FALLBACK_IMAGE] = $this->fallbackImage ;
        return $assetData ;
    }

    public function display(): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        return $this->displayManager ;
    }
}