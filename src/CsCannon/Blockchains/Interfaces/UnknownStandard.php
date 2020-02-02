<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 01/10/2019
 * Time: 19:16
 */

namespace CsCannon\Blockchains\Interfaces;


use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Orb;

class UnknownStandard extends BlockchainContractStandard
{

    public function resolveAsset(Orb $orb)
    {
        return null ;
    }

    public function getStandardName()
    {
        return "Unknown Contract Standard";
    }

    public function getDisplayStructure()
    {
        return null ;
    }

    public function getInterfaceAbi()
    {

        $strJsonFileContents = "[
    {
      
    }
  ]";
        return $strJsonFileContents ;
    }

}