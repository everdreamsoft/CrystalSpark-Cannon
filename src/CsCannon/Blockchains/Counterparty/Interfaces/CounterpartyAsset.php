<?php

namespace CsCannon\Blockchains\Counterparty\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class CounterpartyAsset extends BlockchainContractStandard
{

    public $tokenId = null ;
    public $specificatorArray = ['tokenId'];

    public function __construct()
    {
        $this->solver = DefaultEthereumSolver::class ;





    }




    public function setTokenId($tokenId)
    {
        return ;

    }

    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){

        $this->tokenId = $tokenPath['tokenId'];

       parent::setTokenPath($tokenPath);

    }



    public function getStandardName()
    {
       return "Counterparty Token";
    }

    public function getSolver()
    {

    }

    public function resolveAsset(Orb $orb)
    {

       $return = DefaultEthereumSolver::resolveAsset($orb,$this);
       return $return ;
    }


    public function getDisplayStructure()
    {

        $return = null ;
        return $return ;
    }


}