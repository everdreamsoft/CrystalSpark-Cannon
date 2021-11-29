<?php


namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use CsCannon\Displayable;
use CsCannon\DisplayManager;
use SandraCore\Entity;

class BlockchainTransaction extends Entity implements Displayable
{

    public DisplayManager $displayManager ;


    const DISPLAY_TX = 'txId';
    const DISPLAY_TIMESTAMP = 'timestamp';

    const DISPLAY_BLOCKCHAIN = 'blockchain';
    const DISPLAY_BLOCK = 'on_block';
    const DISPLAY_EVENTS = 'joined_events';


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
     * @return BlockchainEvent[]|null
     */
    public function getEvents(): ?array
    {
        return $this->getJoinedEntities(BlockchainTransactionFactory::JOINED_EVENT);
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


    /**
     * @param BlockchainEvent $event
     * @param null $refArray
     * @param bool $autoCommit
     * @return Entity
     */
    public function addEvent(BlockchainEvent $event, $refArray = null, bool $autoCommit = true): Entity
    {
        return $this->setBrotherEntity(BlockchainTransactionFactory::JOINED_EVENT, $event, $refArray, $autoCommit);
    }


    /**
     * @param DisplayManager $display
     * @return array
     */
    public function returnArray(DisplayManager $display): array
    {
        $blockchain = $this->getBlockchain();

        $return[self::DISPLAY_TX] = $this->getTxId();
        $return[self::DISPLAY_TIMESTAMP] = $this->getTimestamp();
        $return[self::DISPLAY_BLOCKCHAIN] = $blockchain::NAME;
        $return[self::DISPLAY_BLOCK] = $this->getBlock()->getId();

        $events = [];
        foreach ($this->getEvents() ?? [] as $event){
            $events[] = $event->display()->return();
        }

//        $events = [];
//        foreach ($this->getJoinedEvents() ?? [] as $event){
//            $eventDisplay = $event->display(false);
//            $events[] = $event->returnArray($eventDisplay);
//        }

        $return[self::DISPLAY_EVENTS] = $events;

        return $return;
    }


    /**
     * @return DisplayManager
     */
    public function display(): DisplayManager
    {
        if(!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        return $this->displayManager;
    }
}