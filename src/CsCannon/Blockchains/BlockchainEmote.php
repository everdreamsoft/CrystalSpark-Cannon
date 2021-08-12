<?php

namespace CsCannon\Blockchains;

use SandraCore\Entity;
use SandraCore\Reference;

class BlockchainEmote extends Entity
{

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, \SandraCore\System $system)
    {
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
    }


    /**
     * @return string|null
     */
    public function getEmoteId(): ?string
    {
        return $this->getReference(BlockchainEmoteFactory::EMOTE_ID)->refValue ?? null;
    }

    /**
     * @return string|null
     */
    public function getTxHash(): ?string
    {
        return $this->getReference(Blockchain::$txidConceptName)->refValue ?? null;
    }

    /**
     * @return string|null
     */
    public function getBlockTime(): ?string
    {
        return $this->getReference(BlockchainEmoteFactory::EVENT_BLOCK_TIME)->refValue ?? null;
    }

    /**
     * @return string|null
     */
    public function getUnicode(): ?string
    {
        return $this->getReference(BlockchainEmoteFactory::EMOTE_UNICODE)->refValue ?? null;
    }

    /**
     * @return BlockchainAddress|null
     */
    public function getSourceAddress(): ?BlockchainAddress
    {
        $sourceAddresses = $this->getJoinedEntities(BlockchainEmoteFactory::EMOTE_SOURCE_ADDRESS);
        return is_null($sourceAddresses) ? null : end($sourceAddresses);
    }

    /**
     * @return BlockchainContract|null
     */
    public function getTargetContract(): ?BlockchainContract
    {
        $targetContracts = $this->getJoinedEntities(BlockchainEmoteFactory::TARGET_CONTRACT);
        return is_null($targetContracts) ? null : end($targetContracts);
    }

    /**
     * @return BlockchainContractStandard|null
     */
    public function getTargetToken(): ?BlockchainContractStandard
    {
        $targetTokens = $this->getJoinedEntities(BlockchainEmoteFactory::TARGET_TOKEN);
        return is_null($targetTokens) ? null : end($targetTokens);
    }

}