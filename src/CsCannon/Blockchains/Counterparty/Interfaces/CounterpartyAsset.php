<?php

namespace CsCannon\Blockchains\Counterparty\Interfaces;
use CsCannon\AssetSolvers\BooSolver;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
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
class CounterpartyAsset extends BlockchainContractStandard
{

    public $tokenId = null ;
    public $specificatorArray = [];

    public function __construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget,System $system)
    {
        $this->solver = BooSolver::class ;

        parent::__construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget, $system);





    }




    public function setTokenId($tokenId)
    {
        return ;

    }

    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){



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

       $return = BooSolver::resolveAsset($orb,$this);
       return $return ;
    }


    public function getDisplayStructure()
    {

        $return = null ;
        return $return ;
    }


}