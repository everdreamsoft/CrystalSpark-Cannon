<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;

class OrbFactory
{

    //public static  $orbMap, $contractMap, $collectionMap, $assetMap ;


    public   $instanceOrbMap, $instanceContractMap, $instanceCollectionMap, $quantityMap ;

    /**
     *
     * @var Asset[]
     */
    public $instanceAssetMap   ;

    public function getOrbsInCollection(AssetCollection $assetCollection,$limit,$offset){




    }

    public  function getOrbsFromContractPath(BlockchainContract $contract, $path,$quantity=null){

        //in order to know find relevant asset we need to get the collection list
        $collectionArray = $contract->getCollections();

        if(!is_array($collectionArray)) return null ;

        $orbArray = array();

        foreach ($collectionArray as $collection){
            /** @var AssetCollection $collection */
            if (!$collection instanceof AssetCollection) continue ;

            //first we get the specifier
            $solvers = $collection->getSolvers();
            foreach ($solvers ? $solvers : array() as $solver) {
                /** @var AssetSolver $solver  */
                if (!$solver instanceof AssetSolver) continue ;
                $assets = $solver::resolveAsset($collection,$path,$contract);
                foreach ($assets ? $assets : array() as $asset) {

                    $orb = new Orb($contract, $path, $collection, $asset);
                    self::mapOrb($orb, $this,$quantity);

                    $orbArray[] = $orb;
                }
            }


        }

        return $orbArray ;


    }

    public  function getOrbFromSpecifier(BlockchainContractStandard $specifier,BlockchainContract $contract,AssetCollection $collection){

        //in order to know find relevant asset we need to get the collection list


        $orbs =  $this->getOrbsFromContractPath($contract,$specifier);


        return $orbs ;




    }

    public static function mapOrb(Orb $orb, OrbFactory $instance = null,$quantity = null){

        $contract = $orb->contract;
        $collection = $orb->assetCollection ;

        //$objectId = spl_object_hash($orb);



        //self::$orbMap[$orb->orbId] = $orb ;
      //  self::$contractMap[$orb->contract->getId()][$orb->orbId] = $orb ;
       // self::$collectionMap[$orb->assetCollection->getId()][$orb->orbId] = $orb ;
      //  self::$collectionMap[$orb->assetCollection->getId()][$orb->orbId] = $orb ;
     //   self::$assetMap[$orb->asset->id][$orb->orbId] = $orb;



        if ($instance) {

            if($quantity)
                $instance->quantityMap[$orb->orbId] = $quantity;

            $instance->instanceOrbMap[$orb->orbId] = $orb;
            $instance->instanceContractMap[$orb->contract->getId()][$orb->orbId] = $orb;
            $instance->instanceCollectionMap[$orb->assetCollection->getId()][$orb->orbId] = $orb;

            $instance->instanceAssetMap[$orb->asset->id][$orb->orbId] = $orb;
        }



        return $orb ;


    }

    public static function generateOrbCode (Orb $orb){

        return  $orb->contract->getId().$orb->tokenSpecifier->getDisplayStructure().$orb->assetCollection->getId();


    }


    public static function convertOrbsToCSV ($array){




    }


    /**
     * @param Asset $asset
     * @return Orb[]
     */
    public  function getOrbsFromAsset (Asset $asset):array{


        $map = $this->instanceAssetMap ;
        return $map[$asset->id] ?? array();


    }

}