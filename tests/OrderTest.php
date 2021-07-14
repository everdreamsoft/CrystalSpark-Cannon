<?php

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Blockchains\Substrate\Kusama\KusamaAddress;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{

    public function testKusamaMatch()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $factory = new BlockchainOrderFactory($blockchain);

        $addressFactory = $blockchain->getAddressFactory();
        /** @var BlockchainAddress $firstAddress */
        $firstAddress = $addressFactory->createNew(['myFirstKusamaAddress']);
        /** @var BlockchainAddress $secondAddress */
        $secondAddress = $addressFactory->createNew(['mySecondKusamaAddress']);

        $this->createOrder('contractSell', '00000SELL', 5, 'buyContract', '00000BUY', 3, 'txTestSell', 11122233, $factory, $firstAddress);
        $this->createOrder('buyContract', '00000BUY', 10, 'contractSell', '00000SELL', 5, "txTestBuy", 1112223345, $factory, $secondAddress, $firstAddress);

//        $orderFactory = new BlockchainOrderFactory($blockchain);
//        $orderFactory->populateLocal();

        $orderProcess = $blockchain->getOrderProcess();
        $matches = $orderProcess->getAllMatches();

        $orderFactory = new BlockchainOrderFactory($blockchain);
        $orderFactory->populateLocal();

        $this->assertNotEmpty($matches);

        $lastMatch = end($matches);
        /** @var BlockchainOrder $match */
        $match = end($lastMatch);

        $isClose = $match->getReference(BlockchainOrderFactory::STATUS);
        $this->assertNotNull($isClose);
        $this->assertEquals(BlockchainOrderFactory::CLOSE, $isClose->refValue);

        $remainingTotal = $match->getTotal();
        $this->assertEquals(0, $remainingTotal);

        $eventFactory = $blockchain->getEventFactory();
        $eventFactory->populateLocal();
        $events = $eventFactory->getEntities();

        $this->assertCount(2, $events);

        $brother = $match->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
        $this->assertNotNull($brother);

        $joinedEntity = $match->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);
        print_r($joinedEntity);

        // TODO MATCH_WITH

    }


    private function createOrder(string $contractToSell, string $snToSell, int $sellAmount, string $contractToBuy, string $snToBuy, int $buyAmount, string $txHash, int $timestamp, BlockchainOrderFactory $blockchainOrderFactory, BlockchainAddress $source, BlockchainAddress $buyDestination = null): BlockchainOrder
    {
        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $buyContract = $blockchain->getContractFactory()::getContract($contractToBuy,true, RmrkContractStandard::getEntity());
        $sellContract = $blockchain->getContractFactory()::getContract($contractToSell,true, RmrkContractStandard::getEntity());
        $tokenBuy = RmrkContractStandard::init(['sn' => $snToBuy]);
        $tokenSale = RmrkContractStandard::init(['sn' => $snToSell]);

        $blockchainBlockFactory = new BlockchainBlockFactory($blockchain);

        /** @var BlockchainBlock $currentBlock */
        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME,123456);

        return $blockchainOrderFactory->createOrder(
            $blockchain,
            $source,
            $buyContract,
            $sellContract,
            $buyAmount,
            $sellAmount,
            $buyAmount * $sellAmount,
            $txHash,
            $timestamp,
            $currentBlock,
            $tokenBuy,
            $tokenSale,
            $buyDestination
        );
    }


}