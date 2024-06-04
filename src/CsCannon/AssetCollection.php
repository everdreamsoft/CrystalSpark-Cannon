<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

class AssetCollection extends \SandraCore\Entity
{

    public $id = 'collectionId' ;
    public $name = 'name';
    public $imageUrl = 'imageUrl';
    public $description = 'description';
    public $bannerImage = 'bannerImage';

    const SAMPLE_STORAGE = 'sampleStorage';

    private $displayArray = array();

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->id = $this->get($this->id);
        $this->name = $this->get($this->name);
        $this->imageUrl = $this->get($this->imageUrl);
        $this->description = $this->get($this->description);
        $this->bannerImage = $this->get($this->bannerImage);

        $this->createDisplay();



    }

    public function getId()
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->id ;

    }

    public function setSolver(AssetSolver $assetSolver,$replaceExisting = false)
    {

        if(!$assetSolver->validate()){

            SandraManager::dispatchError(SandraManager::getSandra(),1,3,'invalid solver'.
                get_class($assetSolver).' for collection '. $this->id

                ,$this);
        };

        if ($assetSolver) {
            $additionalSolverParameters = $assetSolver->getAdditionalParam();
            if ($replaceExisting){ // if already has solver remove
                $solversBrothers = $this->getBrotherEntity(AssetCollectionFactory::METADATASOLVER_VERB);
                foreach ($solversBrothers as $solver){
                    $solver->delete();
                }

            }

            $this->setBrotherEntity(AssetCollectionFactory::METADATASOLVER_VERB, $assetSolver, $additionalSolverParameters,true,$replaceExisting);
        }

    }

    public function createDisplay()
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        $this->displayArray['id'] = $this->id ;
        $this->displayArray['name'] =  $this->get('name');
        $this->displayArray['description'] = $this->description ;
        $this->displayArray['imageUrl'] = $this->imageUrl ;
        $this->displayArray['bannerImage'] = $this->bannerImage ;
    }


    public function getDefaultDisplay($simple = false)
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->displayArray ;

    }

    public function getOrbFromSpecifier(BlockchainContractStandard $specifier)
    {

        $orbFactory = new OrbFactory();
        $orbs = $orbFactory->getOrbsFromContractPath($this,$specifier);

    }

    public function setImageUrl($url){

        $this->createOrUpdateRef(AssetCollectionFactory::MAIN_IMAGE,$url);
        $this->imageUrl = $url ;


    }

    public function setName($name){

        $this->createOrUpdateRef(AssetCollectionFactory::MAIN_NAME,$name);
        $this->name = $name ;


    }

    public function setDescription($description){

        $this->setStorage($description);
        $this->createOrUpdateRef(AssetCollectionFactory::DESCRIPTION,$description);
        $this->description = $description;


    }

    public function setOwner(BlockchainAddress $blockchainAddress){

       $this->setBrotherEntity(AssetCollectionFactory::COLLECTION_OWNER,$blockchainAddress,[]);


    }

    public function getOwners(){

       return $this->getJoinedEntities(AssetCollectionFactory::COLLECTION_OWNER);


    }


    public  function storeSample($array)
    {

        $factory = $this->factory;

        $json = BlockchainContractStandard::getJsonFromStandardArray($array);
        /** @var EntityFactory $factory */
        $sampleStore = $this->setBrotherEntity('store',self::SAMPLE_STORAGE,null);
        $sampleStore->setStorage($json);


    }

    public  function getStoredSamples()
    {

        $factory = $this->factory;


        /** @var EntityFactory $factory */
        $sampleStore = $this->getBrotherEntity('store',self::SAMPLE_STORAGE);
        if (! $sampleStore instanceof Entity) return null ;

        $json = $sampleStore->getStorage();

        return BlockchainContractStandard::getStandardsFromJson($json);




    }

    /**
     * Return an array of assets contained in collection
     *
     * @return Asset[]
     */
    public function getAssets():array{

        $assetFactory = new AssetFactory();
        $assetFactory->setFilter(AssetFactory::$collectionJoinVerb, $this);
        $assetFactory->populateLocal();

        return $assetFactory->getEntities();


    }


    protected  function getSampleAssets()
    {
        $sampleCSV = $this->getStorage();
    }

    /**
     * @return AssetSolver[]
     */
    public function getSolvers(){


        return $this->getJoinedEntities(AssetCollectionFactory::METADATASOLVER_VERB);



    }


}
