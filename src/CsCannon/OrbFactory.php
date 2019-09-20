<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;

class OrbFactory
{

    public static  $orbMap, $contractMap, $collectionMap, $assetMap ;
    public   $instanceOrbMap, $instanceContractMap, $instanceCollectionMap, $instanceAssetMap ;

   public function getOrbsInCollection(AssetCollection $assetCollection,$limit,$offset){




 }

    public  function getOrbsFromContractPath(BlockchainContract $contract, $path){

       //in order to know find relevant asset we need to get the collection list
       $collectionArray = $contract->getCollections();

       if(!is_array($collectionArray)) return null ;

       $orbArray = array();

       foreach ($collectionArray as $collection){
           /** @var AssetCollection $collection */



           $orb = new Orb($contract,$path,$collection);
           self::mapOrb($orb,$this);

           $orbArray[] = $orb ;


       }

       return $orbArray ;


    }

    public  function getOrbFromSpecifier(BlockchainContractStandard $specifier,BlockchainContract $contract,AssetCollection $collection){

        //in order to know find relevant asset we need to get the collection list


            $orb = new Orb($contract,$specifier,$collection);
            self::mapOrb($orb,$this);


        return $orb ;




    }

    public static function mapOrb(Orb $orb, OrbFactory $instance = null){

        $contract = $orb->contract;
        $collection = $orb->assetCollection ;

        //$objectId = spl_object_hash($orb);



        self::$orbMap[$orb->orbId] = $orb ;
        self::$contractMap[$orb->contract->getId()][$orb->orbId] = $orb ;
        self::$collectionMap[$orb->assetCollection->getId()][$orb->orbId] = $orb ;
        self::$collectionMap[$orb->assetCollection->getId()][$orb->orbId] = $orb ;
        self::$assetMap[$orb->asset->id][$orb->orbId] = $orb;


        if ($instance) {
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

}