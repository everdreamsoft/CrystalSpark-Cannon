<?php

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainTransaction;
use CsCannon\Blockchains\BlockchainTransactionFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;


class MultipleTransactionsTest extends TestCase
{

    private $firstAddress = 'myFirstKusamaAddress';
    private $secondAddress = 'mySecondKusamaAddress';

    public function testCreateEventsInTransaction()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');
        $addressFactory = $blockchain->getAddressFactory();
        $eventFactory = new BlockchainEventFactory();

        $firstAddress = $addressFactory->get($this->firstAddress, true);
        $secondAddress = $addressFactory->get($this->secondAddress, true);

        $contractFactory = $blockchain->getContractFactory();

        $blockFactory = $blockchain->getBlockFactory();
        /** @var BlockchainBlock $block */
        $block = $blockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME,123456);

        $firstContract = $contractFactory::getContract('firstContract',true, RmrkContractStandard::getEntity());
        $secondContract = $contractFactory::getContract('secondContract',true, RmrkContractStandard::getEntity());

        $events = [];

        $events[] = $eventFactory->create(
            $blockchain,
            $firstAddress,
            $secondAddress,
            $firstContract,
            '0x123456-1',
            '0123456',
            $block
        );

        $events[] = $eventFactory->create(
            $blockchain,
            $secondAddress,
            $firstAddress,
            $secondContract,
            '0x123456-2',
            '0123456',
            $block
        );

        $txFactory = new BlockchainTransactionFactory($blockchain);
        /** @var BlockchainTransaction $tx */
        $tx = $txFactory->createTransaction(
            $blockchain,
            '0x123456',
            '0123456',
            $block,
            $events
        );


        $newTxFactory = new BlockchainTransactionFactory($blockchain);
        $newTxFactory->populateLocal();
        /** @var BlockchainTransaction[] $txs */
        $txs = $newTxFactory->getEntities();

        $myTx = reset($txs);

        $txEvents = $myTx->getJoinedEvents();

        $this->assertNotEmpty($txEvents);

        $this->assertNotEmpty($txs);

    }
}