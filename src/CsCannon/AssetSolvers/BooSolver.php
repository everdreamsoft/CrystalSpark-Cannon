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
use CsCannon\AssetFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use CsCannon\SandraManager;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;

class BooSolver extends AssetSolver
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

           echo"reading db";

       }

      $assetCollectionList  = self::$assetInCollections[$orb->assetCollection->getId()];

       //sub optimal there should be a map for that

        foreach ($assetCollectionList->entityArray as $assetEntity)
        {
            /** @var Asset $assetEntity */

           $contractArray =  $assetEntity->getBrotherEntity(AssetFactory::$tokenJoinVerb);

                //linked contract =
            if (isset($contractArray[$orb->contract->subjectConcept->idConcept])) {

                //$assetEntity = $entities[$orb->contract->subjectConcept->idConcept] ;
                return $assetEntity;
            }




        }



        return null ;






    }



}