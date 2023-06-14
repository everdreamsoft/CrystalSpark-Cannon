<?php

namespace CsCannon\Blockchains\Contracts;

use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Orb;
use SandraCore\Reference;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 15.11.2021
 * Time: 14:40
 */
class ORD extends BlockchainContractStandard
{

    public $tokenId = null;
    public $specificatorArray = ['tokenId'];

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {
        $this->solver = LocalSolver::class;
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
    }

    public function setTokenId($tokenId)
    {
        return $this->setTokenPath(array('tokenId' => $tokenId));
    }

    public function setTokenPath($tokenPath)
    {

        $tokenIdUnid = $this->system->systemConcept->get('tokenId');

        //we check if we got a raw value or a reference
        if (isset ($tokenPath[$tokenIdUnid])) {
            $referenceConceptOrString = $tokenPath[$tokenIdUnid];
            if ($referenceConceptOrString instanceof Reference) {
                $referenceConceptOrString = $referenceConceptOrString->refValue;
            }
            $this->tokenId = $referenceConceptOrString;
        }

        if (isset ($tokenPath['tokenId'])) {
            $referenceConceptOrString = $tokenPath['tokenId'];
            if ($referenceConceptOrString instanceof Reference) {
                $referenceConceptOrString = $referenceConceptOrString->refValue;
            }
            $this->tokenId = $referenceConceptOrString;
        }

        return parent::setTokenPath($tokenPath);

    }

    public function getStandardName()
    {
        return "ORD";
    }

    public static function init($tokenId = null)
    {

        $directTokenId = null;
        $tokenData = null;

        //if the user only send a string then he wants to init with the token id
        if (is_string($tokenId) or is_int($tokenId)) {
            $tokenData = null; //we remove token data array
            $directTokenId = $tokenId;
        }

        if (is_array($tokenId)) $tokenData = $tokenId;

        $return = parent::init($tokenData);
        if (!is_null($directTokenId)) $return->setTokenId($directTokenId); // then we set token id afterwards

        return $return;

    }

    public function resolveAsset(Orb $orb)
    {
        $return = LocalSolver::resolveAsset($orb, $this);
        return $return;
    }

    public function getDisplayStructure()
    {
        $return = 'tokenId-' . $this->specificatorData['tokenId'];
        return $return;
    }

    public function getInterfaceAbi()
    {
        $strJsonFileContents = "[]";
        return $strJsonFileContents;
    }

}
