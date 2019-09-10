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

class Orb
{

    public $contract = null ;
    public $tokenSpecifier = null ;
    public $assetCollection = null ;

    public function __construct(BlockchainContract $contract,BlockchainContractStandard $specifier,AssetCollection $assetCollection,$forceInterface = false)
    {


        $this->contract = $contract ;
        $this->tokenSpecifier = $specifier ;
        $this->assetCollection = $assetCollection ;
    }

    public function getAsset(){

        return $this->tokenSpecifier->resolveAsset($this);




 }

}