<?php

namespace CsCannon\Blockchains;

use CsCannon\Blockchains\BlockchainAddress;
use SandraCore\Entity;
use SandraCore\Reference;

class ChangeIssuer extends Entity
{

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, \SandraCore\System $system)
    {
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
    }


    /**
     * @return BlockchainAddress|null
     */
    public function getNewIssuer(): ?BlockchainAddress
    {
        $newIssuers = $this->getJoinedEntities(ChangeIssuerFactory::NEW_ISSUER);

        if(!is_null($newIssuers)){
            $newIssuer = end($newIssuers);
        }else{
            $newIssuer = null;
        }

        return $newIssuer;
    }


    /**
     * @return bool
     */
    public function isAlreadyReassigned(): bool
    {
        $reassigned = $this->getReference(ChangeIssuerFactory::IS_REASSIGNED)->refValue ?? null;

        if(is_null($reassigned)){
            return false;
        }

        return $reassigned == ChangeIssuerFactory::REASSIGNED;
    }


    /**
     * @return string|null
     */
    public function getCollectionId(): ?string
    {
        return $this->getReference(ChangeIssuerFactory::COLLECTION_ID)->refValue ?? null;
    }


    /**
     * @param string $refValue
     * @return Reference
     */
    public function setReassigned(string $refValue = ChangeIssuerFactory::REASSIGNED): Reference
    {
        return $this->createOrUpdateRef(ChangeIssuerFactory::IS_REASSIGNED, $refValue);
    }



}