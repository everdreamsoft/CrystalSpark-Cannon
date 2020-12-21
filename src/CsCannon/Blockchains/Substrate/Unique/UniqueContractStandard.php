<?php

namespace CsCannon\Blockchains\Substrate\Unique;


use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Orb;
use CsCannon\Blockchains\Substrate\SubstrateContractStandard;

class UniqueContractStandard extends SubstrateContractStandard
{
    public $specificatorArray = ['tokenId'];

    public function resolveAsset(Orb $orb)
    {
        //We don't need that for the moment

    }

    public function getStandardName()
    {
        return "UniqueToken";
    }

    public function getDisplayStructure()
    {
        return   $return = 'tokenId-'.$this->specificatorData['tokenId'] ;
    }


    /*
     *
     *
     *
     *
     */
    public function setTokenId($tokenId)
    {
        return $this->setTokenPath(array('tokenId'=>$tokenId));

    }


}