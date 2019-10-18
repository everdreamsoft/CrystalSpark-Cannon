<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
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
class ERC721 extends EthereumContractStandard
{

    public $tokenId = null ;
    public $specificatorArray = ['tokenId'];

    public function __construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget,System $system)
    {
        $this->solver = DefaultEthereumSolver::class ;

        parent::__construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget, $system);





    }




    public function setTokenId($tokenId)
    {
        $this->setTokenPath(array('tokenId'=>$tokenId));

    }

    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){

        $this->tokenId = $tokenPath['tokenId'];

       parent::setTokenPath($tokenPath);

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


    public function getDisplayStructure()
    {

       $return = 'tokenId-'.$this->tokenId ;
        return $return ;
    }


}