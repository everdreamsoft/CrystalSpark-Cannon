<?php


namespace CsCannon\Blockchains;

use CsCannon\Displayable;
use CsCannon\DisplayManager;
use CsCannon\SandraManager;
use SandraCore\Entity;
use SandraCore\EntityFactory;

class BlockchainTransactionFactory extends EntityFactory implements Displayable
{
    public Blockchain $blockchain;

    public static string $isa = 'blockchainTransaction';
    public static string $file = 'blockchainTransactionFile';

    protected static string $className = 'CsCannon\Blockchains\BlockchainTransaction';

    // brothers
    const ON_BLOCKCHAIN = 'onBlockchain';

    // refs
    const TX_ID = 'txHash';
    const EVENT_BLOCK_TIME = 'timestamp';

    // joined
    const EVENT_BLOCK = 'onBlock';
    const JOINED_EVENTS = 'joinedEvents';
    const JOINED_EVENT = 'joinedEvent';

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct(static::$isa, static::$file, SandraManager::getSandra());

        $this->generatedEntityClass = static::$className;

        $this->blockchain = $blockchain;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $asc
     * @param null $sortByRef
     * @param false $numberSort
     * @return Entity[]
     */
    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC', $sortByRef = null, $numberSort = false): array
    {
        $populated = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);

        $this->joinFactory(BlockchainTransactionFactory::JOINED_EVENT, $this->blockchain->getEventFactory());
        $this->joinPopulate();

        return $populated;
    }


    /**
     * @param Blockchain $blockchain
     * @param string $txId
     * @param $timestamp
     * @param BlockchainBlock $block
//     * @param BlockchainEvent[] $events
     * @param bool $autocommit
     * @return Entity
     */
    public function createTransaction(
        Blockchain $blockchain,
        string $txId,
        $timestamp,
        BlockchainBlock $block,
//        array $events,
        bool $autocommit = true
    ): Entity
    {
        $dataArray[Blockchain::$txidConceptName] = $txId ;
        $dataArray[self::EVENT_BLOCK_TIME] = $timestamp ;

        $triplets[self::ON_BLOCKCHAIN] = $blockchain::NAME;
        $triplets[self::EVENT_BLOCK] = $block;
//        $triplets[self::JOINED_EVENTS] = $events;

        return parent::createNew($dataArray, $triplets, $autocommit);
    }


    /**
     * @param DisplayManager $display
     * @return array
     */
    public function returnArray(DisplayManager $display): array
    {
        $output = [];

        foreach ($this->entityArray ?? [] as $txEntity){
            /** @var BlockchainTransaction $txEntity */
            $output[] = $txEntity->display()->return();
        }

        return $output;
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