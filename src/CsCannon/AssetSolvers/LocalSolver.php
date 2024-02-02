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
use CsCannon\BufferManager;
use CsCannon\MetadataProbe;
use CsCannon\MetadataProbeFactory;
use CsCannon\Orb;
use CsCannon\SandraManager;
use InnateSkills\LearnFromWeb\LearnFromWeb;
use InnateSkills\SandraHealth\MemoryManagement;
use SandraCore\EntityFactory;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;

class LocalSolver extends AssetSolver
{
    private static $assetInCollections = [];
    private static $probeCheckedArray = [];
    private static ?BufferManager $bufferManager = null;

    public static function getSolverIdentifier()
    {
        return "localSolver";
    }

    public static function setBufferManager(BufferManager $bufferManager)
    {
        self::$bufferManager = $bufferManager;
    }

    public static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract): ?array
    {
        //we get target collection
        if (!isset(self::$assetInCollections[$assetCollection->getId()])) {
            $assetFactory = self::$bufferManager && $contract->isExplicitTokenId()
                ? self::$bufferManager->getBufferedAssetFactory($contract)
                : self::getAssetFactory($assetCollection);

            self::$assetInCollections[$assetCollection->getId()] = $assetFactory ;
        }

        $assetCollectionList = (self::$bufferManager && $contract->isExplicitTokenId())
            ? self::$bufferManager->getBufferedAssetFactory($contract)
            : self::$assetInCollections[$assetCollection->getId()];

        return $assetCollectionList->getAssetsFromContract($contract,$specifier,self::$bufferManager);
    }

    private static function getAssetFactory(AssetCollection $assetCollection)
    {

        if (self::$bufferManager && self::$bufferManager->hasDirectContractToAssets()){
            return self::$bufferManager->hasDirectContractToAssets();
        }

        //Sub Optimal
        $assetFactory = new AssetFactory();
        $assetFactory->setFilter(0, $assetCollection);
        $assetFactory->populateLocal();
        $assetFactory->getTriplets();
        $assetFactory->populateBrotherEntities(AssetFactory::$tokenJoinVerb);

        return $assetFactory;
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

    public static function clean()
    {
        self::$assetInCollections = null;
        self::$probeCheckedArray = null;
        self::$bufferManager = null;
    }

    protected static function probeMissingAssets($missingArray, AssetCollection $collection)
    {
        $probeFactory = new MetadataProbeFactory();
        $probeFactory->populateLocal();
        foreach ($missingArray ?? array() as $value){
            foreach ($value as $dataArray){
                $contract = $dataArray['contract']; /** @var BlockchainContract $contract */
                $specifier = $dataArray['specifier'];/** @var BlockchainContractStandard $specifier */

                $probe = self::getOrCreateProbe($collection, $contract, $specifier, $probeFactory);
                $probe->queue($specifier);
            }
        }
    }

    private static function getOrCreateProbe($collection, $contract, $specifier, $probeFactory)
    {
        $identifier = $collection->getId().'-'.$specifier->getDisplayStructure();

        if (!isset(self::$probeCheckedArray[$identifier])) {
            $probe = $probeFactory->get($collection,$contract);
            self::$probeCheckedArray[$identifier] = $probe;
        }

        return self::$probeCheckedArray[$identifier];
    }
}
