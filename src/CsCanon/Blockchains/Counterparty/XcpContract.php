<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;



use App\Asset;
use App\AssetCollection;
use App\AssetCollectionFactory;
use App\AssetFactory;
use CsCanon\Blockchains\Bitcoin\BitcoinAddress;
use CsCanon\Blockchains\BlockchainAddress;
use CsCanon\Blockchains\BlockchainContract;
use CsCanon\Blockchains\BlockchainTokenFactory;
use SandraCore\ForeignEntityAdapter;

class XcpContract extends XcpToken
{

    public static $isa = 'xcpContract';
    public static $file = 'blockchainContractFile';
    public static  $className = 'CsCanon\Blockchains\Counterparty\XcpContract' ;


    public function resolveMetaData (){

        $collectionsArray=array();
        /** @var Asset $assetEntity */
        $assetArray = $this->getJoinedEntities(BlockchainTokenFactory::$joinAssetVerb) ;

        foreach ($assetArray as $assetEntity){

           $collections = $assetEntity->getJoinedEntities(AssetFactory::$collectionJoinVerb);

           if (is_array($collections)){

               foreach($collections as $collectionEntity){

                   /** @var AssetCollection $collectionEntity */
                   $collectionsArray[] = $collectionEntity ;

               }

           }


        }


        if(is_array($assetArray)){

            $firstAsset = reset($assetArray);
           return $firstAsset->getDisplayableCollection($collectionsArray);

        }
        else return array('image'=>'https://static.cryptorival.com/imgs/coins/counterparty.png');





        return array();

    }







}