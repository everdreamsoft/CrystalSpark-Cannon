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

    private static $assetInCollections ;

    public static function resolveAsset(Orb $orb, BlockchainContractStandard $specifier){


        //we get target collection

       if (!isset(self::$assetInCollections[$orb->assetCollection->getId()])) {

           $assetFactory = new AssetFactory();
           $assetFactory->setFilter(0, $orb->assetCollection);
           $assetFactory->populateLocal();
           $assetFactory->getTriplets();
           $assetFactory->populateBrotherEntities(AssetFactory::$tokenJoinVerb);
           $entities = $assetFactory->entityArray ;

           self::$assetInCollections[$orb->assetCollection->getId()] = $assetFactory ;



       }

      $assetCollectionList  = self::$assetInCollections[$orb->assetCollection->getId()];

       //sub optimal there should be a map for that

        foreach ($assetCollectionList->entityArray as $assetEntity)
        {
            /** @var Asset $assetEntity */

            $sandra = SandraManager::getSandra();

            if (!isset($assetEntity->subjectConcept->tripletArray[$sandra->systemConcept->get(AssetFactory::$tokenJoinVerb)])) continue ;

           $contractArray =  $assetEntity->subjectConcept->tripletArray[$sandra->systemConcept->get(AssetFactory::$tokenJoinVerb)];

                //linked contract =
            if (in_array($orb->contract->subjectConcept->idConcept,$contractArray)) {

                //$assetEntity = $entities[$orb->contract->subjectConcept->idConcept] ;
                return $assetEntity;
            }


        }

        return null ;


    }


    protected static function updateSolver()
    {
        return ;
    }
}