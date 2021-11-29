<?php


namespace CsCannon\Blockchains;

use CsCannon\SandraManager;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

class BlockchainTransactionFactory extends EntityFactory
{
    public Blockchain $blockchain;

    public static string $isa = 'blockchainTransaction';
    public static string $file = 'blockchainTransactionFile';

    protected static string $className = 'CsCannon\Blockchains\blockchainTransaction';

    // brothers
    const ON_BLOCKCHAIN = 'onBlockchain';

    // refs
    const TX_ID = 'txHash';
    const EVENT_BLOCK_TIME = 'timestamp';

    // joined
    const EVENT_BLOCK = 'onBlock';
    const JOINED_EVENTS = 'joinedEvents';

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct(self::$isa, self::$file, SandraManager::getSandra());

        $this->blockchain = $blockchain;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $asc
     * @param null $sortByRef
     * @param false $numberSort
     * @return array
     */
    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC', $sortByRef = null, $numberSort = false): array
    {
        $populated = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);

        $this->joinFactory(BlockchainTransactionFactory::JOINED_EVENTS, $this->blockchain->getEventFactory());
        $this->joinPopulate();

        return $populated;
    }


    /**
     * @param Blockchain $blockchain
     * @param string $txId
     * @param $timestamp
     * @param BlockchainBlock $block
     * @param BlockchainEvent[] $events
     * @param bool $autocommit
     * @return Entity
     */
    public function createTransaction(
        Blockchain $blockchain,
        string $txId,
        $timestamp,
        BlockchainBlock $block,
        array $events,
        bool $autocommit = true
    ): Entity
    {
        $dataArray[Blockchain::$txidConceptName] = $txId ;
        $dataArray[self::EVENT_BLOCK_TIME] = $timestamp ;

        $triplets[self::ON_BLOCKCHAIN] = $blockchain::NAME;
        $triplets[self::EVENT_BLOCK] = $block;
        $triplets[self::JOINED_EVENTS] = $events;

        return parent::createNew($dataArray, $triplets, $autocommit);
    }

}