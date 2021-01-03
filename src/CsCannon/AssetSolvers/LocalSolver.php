<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 10:22
 */

namespace CsCannon\AssetSolvers;


use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\MetadataProbe;
use CsCannon\MetadataProbeFactory;
use CsCannon\Orb;
use CsCannon\SandraManager;
use InnateSkills\LearnFromWeb\LearnFromWeb;
use SandraCore\EntityFactory;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;

class LocalSolver extends AssetSolver
{

    /**
     * @var EntityFactory[]
     */
    private static $assetInCollections ;
    private static $probeCheckedArray ;

    public static function getSolverIdentifier(){

        return "localSolver";
    }

    public static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract): ?array{


        $return = null ;
        //we get target collection

        if (!isset(self::$assetInCollections[$assetCollection->getId()])) {

            $assetFactory = new AssetFactory();
            $assetFactory->setFilter(0, $assetCollection);
            $assetFactory->populateLocal();
            $assetFactory->getTriplets();
            $assetFactory->populateBrotherEntities(AssetFactory::$tokenJoinVerb);
            $entities = $assetFactory->entityArray ;

            self::$assetInCollections[$assetCollection->getId()] = $assetFactory ;


        }

        $assetCollectionList  = self::$assetInCollections[$assetCollection->getId()];

        $assets = $assetCollectionList->getAssetsFromContract($contract,$specifier);

        // prove is to heavy for now
        //self::probeMissingAssets($assetCollectionList->returnExplicitNoExistingId(),$assetCollection);



        return  $assets ;



    }

    public static function reloadCollectionItems(AssetCollection $assetCollection)
    {
        if (isset(self::$assetInCollections[$assetCollection->getId()])) {

            unset (self::$assetInCollections[$assetCollection->getId()]);

        }
    }


    protected static function updateSolver()
    {
        return ;
    }

    public static function clean(){

        self::$assetInCollections = null ;
        self::$probeCheckedArray = null ;



    }


    protected static function probeMissingAssets($missingArray, AssetCollection $collection)
    {


        $probeFactory = new MetadataProbeFactory();
        $probeFactory->populateLocal();
        foreach ($missingArray ?? array() as $value){

           foreach ($value as $dataArray){
               $contract = $dataArray['contract']; /** @var BlockchainContract $contract */
               $specifier = $dataArray['specifier'];/** @var BlockchainContractStandard $specifier */

               if (!isset(self::$probeCheckedArray[$collection->getId().'-'.$specifier->getDisplayStructure()])){
                   $probe = $probeFactory->get($collection,$contract);
                   self::$probeCheckedArray[$collection->getId().'-'.$specifier->getDisplayStructure()] = $probe;

               }
               else{
                   $probe = self::$probeCheckedArray[$collection->getId().'-'.$specifier->getDisplayStructure()];
               }
               /** @var MetadataProbe $probe */

                if ($probe)
               $probe->queue($specifier);




           }


        }

    }




}