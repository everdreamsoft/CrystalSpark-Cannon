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
    public $orbId = null ;
    public $orbCode = null ;
    public $asset = null ;

    public function __construct(BlockchainContract $contract,BlockchainContractStandard $specifier,AssetCollection $assetCollection,$forceInterface = false)
    {


        $this->contract = $contract ;
        $this->tokenSpecifier = $specifier ;
        $this->assetCollection = $assetCollection ;



        $this->orbCode = OrbFactory::generateOrbCode($this);
        $this->orbId = $this->orbCode ;
        /**@var \CsCannon\Asset $asset **/
        $this->asset = $specifier->resolveAsset($this);

    }

    public function getAsset(){

        return $this->tokenSpecifier->resolveAsset($this);


 }

}