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

    public static function resolveAsset(Orb $orb, BlockchainContractStandard $specifier){

        $contract = $orb->contract ;
        $collection = $orb->assetCollection ;


        if($collection instanceof AssetCollection) {

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

            $foreignAssetFactory = new ForeignEntityAdapter(null,null,SandraManager::getSandra());
            $assetConcept = new ForeignConcept($collection->id.$specifier->specificatorData['tokenId'],SandraManager::getSandra());

            $id = $protocol.$hostName."/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId'] ;

            $data = array("imageUrl"=>"$protocol$hostName/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId'],
            "metaDataUrl"=>"http://www.metadata.com","id"=>"$id"
            );


            $asset = new Asset($assetConcept,$data,$foreignAssetFactory,$id,AssetFactory::$isa,AssetFactory::$file,SandraManager::getSandra());

            return $asset ;

        }



    }



}