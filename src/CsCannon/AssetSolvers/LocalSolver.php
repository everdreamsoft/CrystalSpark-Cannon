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

        return  $assetCollectionList->getAssetsFromContract($contract,$specifier);



    }


    protected static function updateSolver()
    {
        return ;
    }
}