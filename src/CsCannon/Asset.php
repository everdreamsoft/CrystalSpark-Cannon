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
use CsCannon\Blockchains\Generic\GenericContract;
use CsCannon\Blockchains\Generic\GenericContractFactory;
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
    public const CSCANNON_ID = 'cannon_assetId';
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

    /**
     *
     * when joining an asset to a contract with a specific specifier. For example an erc721 with a specific token id
     * Use BlockchainContractStandard:CsCannon_any to connect with any input such as Erc721 any token id
     *
     * @param BlockchainContract $contract
     * @param BlockchainContractStandard $standard
     * @param false $replaceExisting
     */
    public function bindToContractWithSpecifier(BlockchainContract $contract, BlockchainContractStandard $standard, $replaceExisting = false){

        $this->setBrotherEntity(AssetFactory::$tokenJoinVerb,$contract,$standard->specificatorData,true,$replaceExisting);
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
     * @param bool $replaceExisting
     */
    public function bindToContractWithMultipleSpecifiers(BlockchainContract $contract, array $tokenPaths,$replaceExisting = false){


        foreach ($tokenPaths ?? array() as $tokenPath){

            //System::sandraException(SandraManager::getSandra()->systemError("",self::class,3,"Binding asset with contract specifier not a valid blockchain standard
            //"));
            //continue ;

            $contract->setExplicitTokenId(1);
            $tokenPath->subjectConcept->createTriplet($contract->subjectConcept,$this->subjectConcept,null,$replaceExisting);
            // $this->subjectConcept->createTriplet($contract->subjectConcept,$tokenPath->subjectConcept);


        }


    }


    /**
     *
     * Will return an array of Generic contract. Be careful as you won't receive a blockchain specific contract
     * GenericContract as opposed to EthereumContract. If you manually joined contract factory the function will only
     * return contract from your joined factory
     *
     * @return GenericContract[]
     */
    public function getContracts(){

        $entitiesArray = $this->getJoinedEntities(AssetFactory::$tokenJoinVerb) ;

        //entity not found it might be beause the factory hasn't been joined
        if (!$entitiesArray) {
            $contractFactory = new GenericContractFactory();
            $this->factory->joinFactory(AssetFactory::$tokenJoinVerb, $contractFactory);
            $this->factory->joinPopulate();
            $entitiesArray = $this->getJoinedEntities(AssetFactory::$tokenJoinVerb) ;
        }

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

    /**
     *
     * Will return an array of collections.  If you manually joined collection factory the function will only
     * return collection from your joined factory
     *
     * @return AssetCollection[]
     */
    public function getCollections(){

        $entitiesArray = $this->getJoinedEntities(AssetFactory::$collectionJoinVerb) ;

        //entity not found it might be beause the factory hasn't been joined
        if (!$entitiesArray) {
            $collectionFactory = new AssetCollectionFactory($this->system);
            $this->factory->joinFactory(AssetFactory::$collectionJoinVerb, $collectionFactory);
            $this->factory->joinPopulate();
            $entitiesArray = $this->getJoinedEntities(AssetFactory::$collectionJoinVerb) ;
        }

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