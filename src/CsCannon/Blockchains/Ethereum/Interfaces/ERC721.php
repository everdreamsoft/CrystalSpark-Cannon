<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class ERC721 extends EthereumContractStandard
{

    public $tokenId = null ;
    public $specificatorArray = ['tokenId'];

    public function __construct()
    {
        $this->solver = DefaultEthereumSolver::class ;
        $this->tokenId = $this->specificatorArray['tokenId'];




    }








    public function getStandardName()
    {
       return "ERC721";
    }

    public function getSolver()
    {

    }

    public function resolveAsset(Orb $orb)
    {

       $return = DefaultEthereumSolver::resolveAsset($orb,$this);
       return $return ;
    }


}