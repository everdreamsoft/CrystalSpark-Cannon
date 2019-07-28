<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class EthereumAddress extends BlockchainAddress
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;



    public function getBalance(){


       // dd($this->getAddress());

        $finalArray = array();

        //Xchain
        $foreignAdapter = new ForeignEntityAdapter("https://api.opensea.io/api/v1/assets/?format=json&order_by=current_price&order_direction=a&owner=".$this->getAddress(),'assets',SandraManager::getSandra());

        $assetVocabulary = array('image_url'=>'image',
            'assetName'=>'assetName',
            'name'=>'name',



        );

        $foreignAdapter->flatSubEntity('asset_contract','contract');
        $foreignAdapter->adaptToLocalVocabulary($assetVocabulary);
        $foreignAdapter->populate();

        $system = SandraManager::getSandra();
        $collectionContractsArray = array();

        $collectionFactory = new AssetCollectionFactory($system);
        $collectionFactory->populateLocal();

        $collectionAssetCount = array();
        $return['collections'] = array();

        //dd($collectionFactory->getAllWith('collectionId','0x2aea4add166ebf38b63d09a75de1a7b94aa24163'));

        //I'm  tired so I manualy parse the array because the displayer downs't work
        foreach ($foreignAdapter->entityArray as $entity){

           /** @var Entity $entity */
            //dd($entity->get('image'));
            $random = CommonFunctions::somethingToConcept('random',$system);

          //  $assetEntity = new Asset($random,$entity->entityRefs,$foreignAdapter,$entity->entityId,$assetFactory->entityReferenceContainer,$assetFactory->entityContainedIn,SandraManager::getSandra());

           // dd($assetEntity);

            //$unit['image'] = $value['f:image_url'];
            $displayArray[] = $entity->dumpMeta(); ;

            $contractAddress = $entity->get('contract.address');

            if(!isset($collectionArray[$contractAddress])){

               $collection = $collectionFactory->first($collectionFactory->id,$contractAddress);

               if (is_null($collection)){

                   $collection = $collectionFactory->createFromOpenSeaEntity($entity);

               }
                $collectionArray[$contractAddress] = $collection;

            }
            $collection = $collectionArray[$contractAddress] ;

            if(!isset( $collectionContractsArray[$contractAddress])){
                $contract['address'] = $contractAddress;
                $collectionContractsArray[$contractAddress][] = $contract;
            }


            //$contract['address'] = $contractAddress;
            if(!isset($collectionAssetCount[$contractAddress])){
                $collectionAssetCount[$contractAddress] = 0;
            }
            $collectionAssetCount[$contractAddress]++;

            /** @var AssetCollection $collection */



            $assetEntity['image'] = $entity->get('image');
            $assetEntity['assetId'] = $contractAddress.'-'.$entity->get('token_id');
            $assetEntity['name'] = $entity->get('name');
            $assetEntity['balance'] = 1;

            $tokenContainer['tokenId'] =  $entity->get('token_id');
            $tokenContainer['contract'] =  $contractAddress ;
            $tokenContainer['balance'] =  1 ;

            $assetEntity['tokens'] = $tokenContainer ;


            $return['collections'][$collection->id]['id'] =$contractAddress ;
            $return['collections'][$collection->id]['name'] = $collection->name ;
            $return['collections'][$collection->id]['description'] = $collection->description ;
            $return['collections'][$collection->id]['contracts'] = $collectionContractsArray[$contractAddress] ;
            $return['collections'][$collection->id]['assetCount'] = $collectionAssetCount[$contractAddress] ;


            $return['collections'][$collection->id]['assets'][] = $assetEntity ;



        }

        //put now in correct structure remove the key
        foreach ($return['collections'] as $value){

            $finalArray[] = $value ;

        }

        $return['collections']  = $finalArray;


        return $return ;







}

    public function createForeign(){



        dd("creating foreign");



    }


    public function getBlockchain(): Blockchain
    {
        return new EthereumBlockchain();
    }
}