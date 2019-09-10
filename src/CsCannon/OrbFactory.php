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

   public function getOrbsInCollection(AssetCollection $assetCollection,$limit,$offset){




 }

    public static function getOrbsFromContractPath(BlockchainContract $contract, $path){

       //in order to know find relevant asset we need to get the collection list
       $collectionArray = $contract->getCollections();

       if(!is_array($collectionArray)) return null ;

       $orbArray = array();

       foreach ($collectionArray as $collection){
           /** @var AssetCollection $collection */



           $orb = new Orb($contract,$path,$collection);

           $orbArray[] = $orb ;


       }

       return $orbArray ;


    }

    public static function getOrbFromSpecifier(BlockchainContractStandard $specifier,BlockchainContract $contract,AssetCollection $collection){

        //in order to know find relevant asset we need to get the collection list





            $orb = new Orb($contract,$specifier,$collection);





        return $orb ;




    }

}