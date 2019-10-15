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
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use CsCannon\SandraManager;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;

class DefaultEthereumSolver extends AssetSolver
{

    public static function resolveAsset(AssetCollection $collection, BlockchainContractStandard $specifier, BlockchainContract $contract):array {



        if($collection instanceof AssetCollection) {

            /*

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';



            $id = $protocol.$hostName."/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId'] ;

            $data = array(AssetFactory::IMAGE_URL=>"$protocol$hostName/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId'],
            AssetFactory::METADATA_URL=>"http://www.metadata.com",AssetFactory::ID=>"$id"
            );
            */

            $foreignAssetFactory = new ForeignEntityAdapter(null,null,SandraManager::getSandra());
            $assetConcept = new ForeignConcept($collection->id.$specifier->specificatorData['tokenId'],SandraManager::getSandra());

            $url = "http://alpha.bitcrystals.com/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId'];

            $data = array(AssetFactory::IMAGE_URL=>$url,
                AssetFactory::METADATA_URL=>"http://www.metadata.com",AssetFactory::ID=>"$url"
            );


            $asset = new Asset($assetConcept,$data,$foreignAssetFactory,$url,AssetFactory::$isa,AssetFactory::$file,SandraManager::getSandra());

            return array($asset) ;

        }



    }


    public static function updateSolver()
    {
        return ;
    }
}