<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 10:22
 */

namespace CsCannon\AssetSolvers;


use CsCannon\AssetCollection;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;

class DefaultEthereumSolver extends AssetSolver
{

    public static function resolveAsset(Orb $orb, BlockchainContractStandard $specifier){

        $contract = $orb->contract ;
        $collection = $orb->assetCollection ;


        if($collection instanceof AssetCollection) {

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

            return array("image"=>"$protocol$hostName/api/v1/$collection->id/image/".$specifier->specificatorData['tokenId']);

        }



    }



}