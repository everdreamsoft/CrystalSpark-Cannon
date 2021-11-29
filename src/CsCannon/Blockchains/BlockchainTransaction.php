<?php


namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use SandraCore\Entity;

class BlockchainTransaction extends Entity
{

    public function getBlockchain():Blockchain
    {
        $conceptTriplets = $this->subjectConcept->getConceptTriplets();
        $conceptId = $conceptTriplets[$this->system->systemConcept->get(BlockchainOrderFactory::ON_BLOCKCHAIN)] ?? null;
        $lastId = end($conceptId);
        $blockchainName = $this->system->systemConcept->getSCS($lastId);

        return BlockchainRouting::getBlockchainFromName($blockchainName);
    }


    /**
     * @return BlockchainEvent[]
     */
    public function getJoinedEvents(): array
    {
        return $this->getJoinedEntities(BlockchainTransactionFactory::JOINED_EVENTS);
    }


    /**
     * @return BlockchainBlock|null
     */
    public function getBlock(): ?BlockchainBlock
    {
        $blocks = $this->getJoinedEntities(BlockchainTransactionFactory::EVENT_BLOCK);
        return is_null($blocks) ? null : reset($blocks);
    }


    /**
     * @return string|null
     */
    public function getTxId(): ?string
    {
        return $this->getReference(BlockchainTransactionFactory::TX_ID)->refValue ?? null;
    }


    /**
     * @return string|null
     */
    public function getTimestamp(): ?string
    {
        return $this->getReference(BlockchainTransactionFactory::EVENT_BLOCK_TIME)->refValue ?? null;
    }

}