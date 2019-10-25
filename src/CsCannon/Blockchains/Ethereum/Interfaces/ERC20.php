<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class ERC20 extends EthereumContractStandard
{


    public $specificatorArray = null ;

    public function __construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget,System $system)
    {
        $this->solver = LocalSolver::class ;

        parent::__construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget, $system);

    }






    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){



       parent::setTokenPath($tokenPath);

    }



    public function getStandardName()
    {
       return "ERC20";
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