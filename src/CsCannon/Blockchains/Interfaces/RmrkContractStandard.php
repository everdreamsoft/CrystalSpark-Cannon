<?php


namespace CsCannon\Blockchains\Interfaces;


use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Orb;

class RmrkContractStandard extends BlockchainContractStandard
{

    public $specificatorArray = ['sn'];

    public function resolveAsset(Orb $orb)
    {
        //We don't need that for the moment

    }

    public function getStandardName()
    {
        return "RmrkStandard";
    }

    public function getDisplayStructure()
    {
        return   $return = 'sn-'.$this->specificatorData['sn'] ;
    }


    /*
     *
     *
     *
     *
     */
    public function setSn($sn)
    {
        return $this->setTokenPath(array('sn'=>$sn));

    }

}