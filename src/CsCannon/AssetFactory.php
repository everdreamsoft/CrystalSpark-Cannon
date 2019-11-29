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
use CsCannon\Blockchains\BlockchainTokenFactory;
use SandraCore\Entity;
use SandraCore\System;

class AssetFactory extends \SandraCore\EntityFactory
{


    protected static $className = 'CsCannon\Asset' ;
    public static $isa = 'blockchainizableAsset';
    public static $file = 'blockchainizableAssets';
    public static $tokenJoinVerb = 'bindToContract';
    public static $collectionJoinVerb = 'bindToCollection';
    public const ID = "assetId";
    public const IMAGE_URL = "imgURL";
    public const METADATA_URL = "metaDataURL";
    private $specifierMap ;



    public function __construct(System $system = null)
    {



        if (is_null($system)) $system = SandraManager::getSandra();

        parent::__construct(self::$isa, self::$file, $system);

        $this->generatedEntityClass = self::$className ;
    }

    //legacy (token doesn't exist anymore it's contracts)
    public function joinToken (BlockchainTokenFactory $factory){

        $this->joinFactory(AssetFactory::$tokenJoinVerb,$factory);


    }

    public function joinContract (BlockchainTokenFactory $factory){

        $this->joinFactory(AssetFactory::$tokenJoinVerb,$factory);


    }

    public function joinCollection (AssetCollectionFactory $factory){

        $this->joinFactory(AssetFactory::$collectionJoinVerb,$factory);


    }

    public function getAssetsFromContract(BlockchainContract $contract, BlockchainContractStandard $specifier){

        if (!isset($this->specifierMap[$contract->subjectConcept->idConcept])) {

            $assetList = $this->getEntitiesWithBrother(self::$tokenJoinVerb, $contract->subjectConcept->idConcept);

            foreach ($assetList ? $assetList : array() as $asset){

                $localStorageStandard = $contract->getStandard();
                $standardData = $asset->getBrotherEntity(AssetFactory::$tokenJoinVerb);
                if(is_array($standardData)) {
                    $standardData = end($standardData);
                    $localStorageStandard->setTokenPath($standardData->entityRefs);
                }

                $this->specifierMap[$contract->subjectConcept->idConcept][$localStorageStandard->getDisplayStructure()][] = $asset ;
            }


        }

        return $this->specifierMap[$contract->subjectConcept->idConcept][$specifier->getDisplayStructure()];




    }

    public function get($id):?Asset{

        return $this->first(AssetFactory::ID,$id);

    }


    public function create($id, Array $metaData,array $collections=null,array $contracts=null){



        //Id should be unique in collection
        $verifyFactory = new AssetFactory(SandraManager::getSandra());
        $verifyFactory->populateLocal();
        $verif = $verifyFactory->get($id);

        if (isset($verif)) {
            SandraManager::dispatchError(SandraManager::getSandra(), 2, 2, "Asset 
        with id $id already exists", $this);
            return $verif ;

        }

        $metaData[self::ID] = $id ;
        $brotherEntities = null ;

        //we need to add the meta data on brothers
        foreach ($contracts ? $contracts : array() as $contract){
            /** @var Entity $contract */
            $brotherEntities[self::$tokenJoinVerb][$contract->subjectConcept->idConcept][$this->sc->get('creationTimestamp')] = time();

        }

        //we need to add the meta data on brothers
        foreach ($collections ? $collections : array() as $collection){
            /** @var Entity $collection */
            $brotherEntities[self::$collectionJoinVerb][$collection->subjectConcept->idConcept][$this->sc->get('creationTimestamp')] = time();

        }

        //$brotherEntities = array(self::$tokenJoinVerb =>($contracts),self::$collectionJoinVerb =>($collections));



        $newEntity = $this->createNew($metaData,$brotherEntities);

        //bind the contract to collection



        return $newEntity ;







    }




}