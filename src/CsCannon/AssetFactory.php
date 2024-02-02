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
use PhpParser\PrettyPrinter\Standard;
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
    public const ONCHAIN_METADATA_URL = "onChainMetaDataURL";
    public const ASSET_NAME = "name";
    private $unkownExplicitIds ;
    private $specifierMap ;




    public function __construct(System $system = null)
    {



        if (is_null($system)) $system = SandraManager::getSandra();

        parent::__construct(self::$isa, self::$file, $system);

        $this->generatedEntityClass = self::$className ;
    }



    public function joinCollection (AssetCollectionFactory $factory){

        $this->joinFactory(AssetFactory::$collectionJoinVerb,$factory);


    }

    public function getAssetsFromContract(BlockchainContract $contract, BlockchainContractStandard $specifier, BufferManager $bufferManager=null){


        $localStorageStandard = $contract->getStandard();

        //this is true only when one specifier equals one asset. But not if many tokens equals the same asset
        if (!isset($this->specifierMap[$contract->subjectConcept->idConcept][$specifier->getDisplayStructure()])) {


            $assetList = $this->getEntitiesWithBrother(self::$tokenJoinVerb, $contract->subjectConcept->idConcept);
            //in that case we a specific joined concepts
            if (!$contract->isExplicitTokenId()) {

                foreach ($assetList ? $assetList : array() as $asset) {


                    $standardData = $asset->getBrotherEntity(AssetFactory::$tokenJoinVerb);
                    if (is_array($standardData)) {

                        $standardData = end($standardData);
                        $localStorageStandard = clone($localStorageStandard);
                        $localStorageStandard = $this->replaceAnyOnOmittedData($localStorageStandard,$standardData);

                    }

                    $assetAlreadyInArray = false ;
                    foreach  ($this->specifierMap[$contract->subjectConcept->idConcept][$localStorageStandard->getDisplayStructure()] ?? array() as $existAsset){
                        if ($asset->entityId == $asset->entityId)
                            $assetAlreadyInArray = true ;
                    }
                    if (!$assetAlreadyInArray)
                        $this->specifierMap[$contract->subjectConcept->idConcept][$localStorageStandard->getDisplayStructure()][] = $asset;

                }

            }
            else {
            //Explicit token to contract



                //lt see if we have linked path
                if (!$bufferManager) {
                    $tokenToAssetFactory = new TokenPathToAssetFactory($contract->system);
                    $tokenToAsset = $tokenToAssetFactory->get($specifier->getDisplayStructure());
                    $tokenToAssetFactory->getTriplets();

                    try{
                        $tokenToAssetFactory->populateBrotherEntities($contract);
                    }catch (\Exception $e){

                    }
                }else{
                    $tokenToAssetFactory = $bufferManager->getBufferedTokenToAsset($contract);
                    $tokenToAsset = $tokenToAssetFactory->get($specifier->getDisplayStructure());
                }



                $assetsList = $tokenToAsset->subjectConcept->tripletArray[$contract->subjectConcept->idConcept] ?? array();
                foreach ($assetsList  as $assetId) {

                    if (!isset($this->entityArray[$assetId])) continue ;
                    $this->specifierMap[$contract->subjectConcept->idConcept][$specifier->getDisplayStructure()][] = $this->entityArray[$assetId];;
                }

                //is the asset existing ?
                if (isset($this->specifierMap[$contract->subjectConcept->idConcept][$specifier->getDisplayStructure()]))
                    return $this->specifierMap[$contract->subjectConcept->idConcept][$specifier->getDisplayStructure()];

                //NO asset found
                $this->noAssetOnExplicitId($contract,$specifier);
                return null ;
            }
        }


        if (isset($this->specifierMap[$contract->subjectConcept->idConcept][$this->adaptStandardDisplayStructure($specifier,$localStorageStandard)]))
            return $this->specifierMap[$contract->subjectConcept->idConcept][$this->adaptStandardDisplayStructure($specifier,$localStorageStandard)];

        return null ;





    }

    public function get($id):?Asset{

        return $this->first(AssetFactory::ID,$id);

    }

    public function getOrCreate($id, Array $metaData,array $collections=null,array $contracts=null){

        //Id should be unique in collection
        $verifyFactory = new AssetFactory(SandraManager::getSandra());
        $verifyFactory->populateFromSearchResults($id,AssetFactory::ID);
        $verif = $verifyFactory->get($id);

        if($verif) {
            $this->assetUpdate($verif,$metaData);
            return $verif;
        }

        return $this->forceCreate($id,  $metaData, $collections, $contracts);

    }

    public function assetUpdate(Asset $asset, $metaData){

        foreach ($metaData ?? array() as $keyData => $valueData){

            if ($asset->get($keyData) != $valueData){
                if (!is_string($valueData) && !is_numeric($valueData)) continue ;

                $asset->createOrUpdateRef($keyData,$valueData);
            }

        }

    }


    public function create($id, Array $metaData,array $collections=null,array $contracts=null):Asset{

        //Id should be unique in collection
        $verifyFactory = new AssetFactory(SandraManager::getSandra());
        $verifyFactory->populateFromSearchResults($id,AssetFactory::ID);
        $verif = $verifyFactory->get($id);

        if (isset($verif)) {
            SandraManager::dispatchError(SandraManager::getSandra(), 2, 2, "Asset
        with id $id already exists", $this);
            return $verif ;

        }

        return $this->forceCreate($id,  $metaData, $collections, $contracts);


    }


    private function forceCreate($id, Array $metaData,array $collections=null,array $contracts=null):Asset{



        $metaData[self::ID] = $id ;
        $brotherEntities = null ;

        //we need to add the meta data on brothers
        foreach ($contracts ? $contracts : array() as $key => $contract){
            /** @var Entity $contract */
            if ($contract instanceof BlockchainContract) {
                $brotherEntities[self::$tokenJoinVerb][$contract->subjectConcept->idConcept][$this->sc->get('creationTimestamp')] = time();

            }
            if (is_array($contract)) {
                $brotherEntities[self::$tokenJoinVerb][$key] = $contract;

            }

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



    private function noAssetOnExplicitId(BlockchainContract $contract, BlockchainContractStandard $specifier){

        //I wanted to batch requestes by I will send all raw

        $this->unkownExplicitIds[$contract->getId()][$specifier->getDisplayStructure()]['contract'] = $contract ;
        $this->unkownExplicitIds[$contract->getId()][$specifier->getDisplayStructure()]['specifier'] = $specifier ;



    }

    public function returnExplicitNoExistingId(){

        return $this->unkownExplicitIds;
    }

    private function adaptStandardDisplayStructure(BlockchainContractStandard $standard,BlockchainContractStandard $localStorageStandard){

        $specifierArray = $standard->specificatorData ;
        $standardModified = clone($standard);

        foreach ($localStorageStandard->specificatorData ?? array() as $key => $requiredData){

            if ($requiredData == BlockchainContractStandard::CS_CANNON_ANY){
                $specifierArray[$key] = $requiredData ;
            }

        }
        $standardModified->setTokenPath($specifierArray);

        return $standardModified->getDisplayStructure();

    }

    private function replaceAnyOnOmittedData(BlockchainContractStandard $standard,$standardData){

        $outputArray = $standard->specificatorData;
        $standard = clone($standard);
        foreach ($standard->specificatorArray ?? array() as $keyToExist){

            if (isset($standardData->entityRefs[$keyToExist])){

                $outputArray[$keyToExist] = $standardData[$keyToExist] ;
            }

            if (!isset($standard->specificatorData[$keyToExist])){

                $outputArray[$keyToExist] = BlockchainContractStandard::CS_CANNON_ANY ;
            }



        }

        $standard->setTokenPath($outputArray);

        return $standard ;

    }









}
