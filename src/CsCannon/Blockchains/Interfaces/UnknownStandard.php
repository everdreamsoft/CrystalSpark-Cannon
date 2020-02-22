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
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\Reference;

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

    public function verifyTokenPath($tokenPath)
    {
        foreach ($tokenPath ? $tokenPath : array() as $key => $referenceConceptOrString){

            $shortName = 'error';

            if ($referenceConceptOrString instanceof Reference){
                $referenceConceptOrString = $referenceConceptOrString->refValue;
            }

            if (is_numeric($key)){
                $shortName = $this->system->systemConcept->getSCS($key);
            }
            else if (is_string($key)){
                $shortName = $key;
            }



            $this->specificatorData[$shortName] = $referenceConceptOrString ;

        }
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