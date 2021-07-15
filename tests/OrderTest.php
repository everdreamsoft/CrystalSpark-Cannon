<?php

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;
use SandraCore\Entity;

class OrderTest extends TestCase
{

    private int $contractQuantity = 5;
    private int $ksmQuantity = 3;

    public function testKusamaMatch()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $this->makeKusamaMatchOrders();

        $orderFactory = new BlockchainOrderFactory($blockchain);
        $orderFactory->populateWithMatch();

        $matchedOrders = $orderFactory->getEntities();

        $this->assertNotEmpty($matchedOrders);

        $matches = [];

        foreach ($matchedOrders as $matchOrder){
            $joined = $matchOrder->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);
            if(!is_null($joined)){
                $matches[] = $matchOrder;
            }
        }

        $this->assertNotEmpty($matches);
        /** @var BlockchainOrder $match */
        $match = end($matches);

        $isClose = $match->getReference(BlockchainOrderFactory::STATUS);
        $this->assertNotNull($isClose);
        $this->assertEquals(BlockchainOrderFactory::CLOSE, $isClose->refValue);

        $remainingTotal = $match->getTotal();
        $this->assertEquals(0, $remainingTotal);

        $eventFactory = $blockchain->getEventFactory();
        $eventFactory->populateLocal();
        $events = $eventFactory->getEntities();

        $this->assertCount(1, $events);


        /** @var Entity[] $brothers */
        $brothers = $match->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
        $this->assertNotNull($brothers);

        $brother = end($brothers);

        $matchQuantity = $brother->getReference(BlockchainOrderFactory::MATCH_BUY_QUANTITY)->refValue;
        $this->assertEquals($this->contractQuantity, $matchQuantity);

        $matchKsm = $brother->getReference(BlockchainOrderFactory::MATCH_SELL_QUANTITY)->refValue;
        $this->assertEquals($this->ksmQuantity, $matchKsm);



        $matchedOrders = $match->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);
        $this->assertNotNull($matchedOrders);

        /** @var BlockchainOrder $orderMatched */
        $orderMatched = end($matchedOrders);

        $remainingTotal = $orderMatched->getTotal();
        $this->assertEquals(0, $remainingTotal);

    }



    public function testViewOrders()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $this->makeKusamaMatchOrders();

        $orderFactory = new BlockchainOrderFactory($blockchain);
        $orderFactory->populateWithMatch();
        $view = $orderFactory->viewAllOrdersOnChain($blockchain);


    }




    private function makeKusamaMatchOrders()
    {

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $factory = new BlockchainOrderFactory($blockchain);

        $addressFactory = $blockchain->getAddressFactory();
        /** @var BlockchainAddress $firstAddress */
        $firstAddress = $addressFactory->createNew(['myFirstKusamaAddress']);
        /** @var BlockchainAddress $secondAddress */
        $secondAddress = $addressFactory->createNew(['mySecondKusamaAddress']);

        $this->createOrder($blockchain, 'contractSell', '00000SELL', $this->contractQuantity, 'KSM', null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress);
        $this->createOrder($blockchain, 'KSM', null, $this->ksmQuantity, 'contractSell', '00000SELL', $this->contractQuantity, "txTestBuy", 1112223345, $factory, $secondAddress, $firstAddress);

        $orderProcess = $blockchain->getOrderProcess();
        $orderProcess->getAllMatches();
    }



    private function createOrder(Blockchain $blockchain, string $contractToSell, $snToSell, int $sellAmount, string $contractToBuy, $snToBuy, int $buyAmount, string $txHash, int $timestamp, BlockchainOrderFactory $blockchainOrderFactory, BlockchainAddress $source, BlockchainAddress $buyDestination = null): BlockchainOrder
    {

        $buyContract = $blockchain->getContractFactory()::getContract($contractToBuy,true, RmrkContractStandard::getEntity());
        $sellContract = $blockchain->getContractFactory()::getContract($contractToSell,true, RmrkContractStandard::getEntity());

        if(!is_null($snToBuy)){
            $snToBuy = RmrkContractStandard::init(['sn' => $snToBuy]);
        }

        if(!is_null($snToSell)){
            $snToSell = RmrkContractStandard::init(['sn' => $snToSell]);
        }

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
            $snToBuy,
            $snToSell,
            $buyDestination
        );
    }


}