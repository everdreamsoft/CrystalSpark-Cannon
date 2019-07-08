<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains;




use CsCanon\Asset;
use CsCanon\AssetCollectionFactory;
use CsCanon\AssetFactory;
use CsCanon\Token;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

abstract class  BlockchainAddress extends Entity
{

   protected $address ;
   public $assetList = array();

    abstract public function getBalance();

    public function setAddress($address){

        $this->address = $address;


    }

    protected function getAddress(){

        return $this->address ;


    }

    protected function returnBalance(ForeignEntityAdapter $foreignAdapter, BlockchainTokenFactory $tokenFactory){

        $system = app('Sandra')->getSandra();
        //$tokenFactory->getAllWith('tokenId','SATOSHICARD');
        $tokenFactory->getTriplets();
       // dd($tokenFactory);


        foreach ($foreignAdapter->entityArray as $assetEntity){

            $balanceInfo = array();
            //if ($assetEntity->get('tokenId') != 'SATOSHICARD') continue ;

            $tokenId = $assetEntity->get('tokenId') ;

            $balanceInfo['tokenId'] = '';

            $tokenExistsEntity = $tokenFactory->first('tokenId',$assetEntity->get('tokenId'));
            $balanceInfo['balance'] = $assetEntity->get('balance') ;
            $balanceInfo['tokenId'] =  $assetEntity->get('tokenId') ;

            try{

                $balanceInfo['tokenName'] = $assetEntity->get('tokenName') ;
            }
            catch (\Exception $e){}





            $balanceIndex[$tokenId] =  $assetEntity->get('balance') ;





            if(!$tokenExistsEntity) {

                $tokenExistsEntity = $tokenFactory->create($assetEntity->get('tokenId'));

            }

            $balanceInfo['contract'] = $tokenExistsEntity->contract ;

            $unavailableEntities[] = $balanceInfo;

        }



        $assetFactory = new AssetFactory($system);

        $assetCollection = new AssetCollectionFactory($system);


        $tokenFactory->joinAsset($assetFactory);
        $tokenFactory->joinPopulate();

        $assetFactory->joinCollection($assetCollection);
        $assetFactory->joinPopulate();



        foreach ($tokenFactory->entityArray as $token) {

            $count = 0;

            //echo $token->getJoined(BlockchainTokenFactory::$joinAssetVerb,'tokenId');
           //

            if ($token->getJoinedEntities(BlockchainTokenFactory::$joinAssetVerb)) {
                $array = $token->getJoinedEntities(BlockchainTokenFactory::$joinAssetVerb);

                //dd(count($array));
                foreach ($array as $asset) {
                    /** @var Asset $asset */
                    $count++ ;




                    $collections = $asset->getJoinedEntities(AssetFactory::$collectionJoinVerb);
                    //each collection of one asset
                if (!$collections){

                    continue ;}
                foreach ($collections as $collection){
                    $assetContainer = $asset->getDisplayable();
                    $tokenId =  $token->get('tokenId');
                    //$assetContainer['tokenId'] = $token->get('tokenId');
                    $assetContainer['assetId'] =  $asset->get('assetId') ;
                    $assetContainer['unid'] =  $asset->subjectConcept->idConcept ;
                    $assetContainer['name'] = $token->get('tokenId');



                    $tokenContainer['tokenId'] =  $token->get('tokenId');
                    $tokenContainer['balance'] =  $balanceIndex[$tokenId] ;
                    $tokenContainer['contract'] =  $token->contract ;


                    $assetContainer['tokens'] = $tokenContainer ;



                    //$collectionsContainer[$collection->id] =
                    $reponse['collections'][$collection->id]['id']=  $collection->id ;
                    $reponse['collections'][$collection->id]['name']=  $collection->name ;
                    $reponse['collections'][$collection->id]['description']=  $collection->description ;
                    if(isset( $reponse['collections'][$collection->id]['assetCount'])) {
                        $reponse['collections'][$collection->id]['assetCount']++;
                    }
                    else   $reponse['collections'][$collection->id]['assetCount'] = 1 ;
                    $reponse['collections'][$collection->id]['assets'][] =  $assetContainer ;



                }


                }

                // $result['collections'][]
            }
        }

//put now in correct structure remove the key
        if(isset($response) && isset($reponse['collections'])) {
            foreach ($reponse['collections'] as $value) {

                $finalArray[] = $value;

            }

            $reponse['collections'] = $finalArray;
        }






       // dd($tokenFactory);




        $reponse['assets'] = $tokenFactory->return2dArray() ;
        $reponse['tokens'] = $unavailableEntities ;

        return $reponse ;



    }




}