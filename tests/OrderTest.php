<?php

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Blockchains\Substrate\Kusama\KusamaAddress;
use CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory;
use CsCannon\SandraManager;
use CsCannon\Tests\TestManager;
use CsCannon\Tools\BalanceBuilder;
use PHPUnit\Framework\TestCase;
use SandraCore\Concept;
use SandraCore\ConceptFactory;
use SandraCore\Entity;


class OrderTest extends TestCase
{

    private $contractQuantity = 1;
    private $ksmQuantity = 1;

    private $firstAddress = 'myFirstKusamaAddress';
    private $secondAddress = 'mySecondKusamaAddress';
    private $snSell = '00000SELL';

    public function testMatchLastBuy()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $addressFactory = $blockchain->getAddressFactory();
        $factory = new BlockchainOrderFactory($blockchain);

        $firstAddress = $addressFactory->get($this->firstAddress, true);
        $secondAddress = $addressFactory->get($this->secondAddress, true);

        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress);
        $this->createOrder($blockchain, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'contractSell', $this->snSell, $this->contractQuantity, "txTestBuy", 1112223345, $factory, $secondAddress, $firstAddress);

        // check search without status and with buyDestination have only one result
        $factory = new BlockchainOrderFactory($blockchain);
        $factory->setFilter(BlockchainOrderFactory::STATUS, 0, true);
        $factory->setFilter(BlockchainOrderFactory::BUY_DESTINATION);
        $factory->populateLocal(1);
        /** @var BlockchainOrder[] $orders */
        $orders = $factory->getEntities();

        $this->assertCount(1, $orders);


        $orderProcess = $blockchain->getOrderProcess();
        $orderProcess->makeMatchOneByOne();

        // check the both orders have a status
        $orderFactory = new BlockchainOrderFactory($blockchain);
        $orderFactory->setFilter(BlockchainOrderFactory::STATUS);
        $orderFactory->populateLocal();
        /** @var BlockchainOrder[] $orders */
        $orders = $orderFactory->getEntities();

        foreach ($orders as $order){
            $status = $order->getBrotherEntity(BlockchainOrderFactory::STATUS);
            $this->assertNotNull($status);
        }

        $eventFactory = new KusamaEventFactory();
        $eventFactory->populateLocal();

        /** @var BlockchainEvent[] $events */
        $events = $eventFactory->getEntities();
        $event = end($events);

        $specifier = $event->getSpecifier();
        $this->assertNotNull($specifier);

        $this->assertCount(1, $events);
        $this->assertEquals(strtolower($this->firstAddress), $event->getSourceAddress()->getAddress());
        $this->assertEquals(strtolower($this->secondAddress), $event->getDestinationAddress()->getAddress());
        $this->assertEquals('sn-'.$this->snSell, $event->getSpecifier()->getDisplayStructure());
    }


    public function testLastListCancel()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');
        $factory = new BlockchainOrderFactory($blockchain);
        $addressFactory = $blockchain->getAddressFactory();
        $firstAddress = $addressFactory->get($this->firstAddress, true);

        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress, null, false);
        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, 0, 'txTestSells', 11122234, $factory, $firstAddress, null, false);
        $this->createOrder($blockchain, 'newContract', '00000CANCEL', $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, 0, 'txTestCancel', 11122235, $factory, $firstAddress, null, false);


        $orderProcess = $blockchain->getOrderProcess();
        $result = $orderProcess->listsCancellation($blockchain);

        $this->assertTrue($result);

        if($result){
            $factory = new BlockchainOrderFactory($blockchain);
            $factory->setFilter(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CANCELLED);
            $factory->populateLocal();
            $orders = $factory->getEntities();

            $this->assertNotEmpty($orders);
            $this->assertCount(2, $orders);
        }

    }


    public function testListCancellation()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $addressFactory = $blockchain->getAddressFactory();
        $factory = new BlockchainOrderFactory($blockchain);

        $firstAddress = $addressFactory->get($this->firstAddress, true);

        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress, null, false);
        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, 0, 'txTestSell', 11122234, $factory, $firstAddress, null, false);

        $orderProcess = $blockchain->getOrderProcess();
        $cancelled = $orderProcess->cancelLists(10, $blockchain);
        $this->assertTrue($cancelled);

        $orderfactory = new BlockchainOrderFactory($blockchain);
        $orderfactory->populateLocal();
        $orders = $orderfactory->getEntities();

        $this->assertCount(2, $orders);

        foreach ($orders as $order){
            $status = $order->getBrotherEntity(BlockchainOrderFactory::STATUS);
            $this->assertNotNull($status);
        }

        $newOrderFact = new BlockchainOrderFactory($blockchain);
        $newOrderFact->setFilter(BlockchainOrderFactory::STATUS, 0, true);
        $newOrderFact->populateLocal();
        $orders = $newOrderFact->getEntities();

        $this->assertCount(0, $orders);

    }



    public function testOrdersTreatment()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $addressFactory = $blockchain->getAddressFactory();
        $factory = new BlockchainOrderFactory($blockchain);

        $firstAddress = $addressFactory->get($this->firstAddress, true);
        $secondAddress = $addressFactory->get($this->secondAddress, true);


        // create orders

        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress);
        $this->createOrder($blockchain, $blockchain->getMainCurrencyTicker(), null, $this->ksmQuantity, 'contractSell', $this->snSell, $this->contractQuantity, "txTestBuy", 1112223345, $factory, $secondAddress, $firstAddress);


        // transform orders to event

        $orderProcess = $blockchain->getOrderProcess();
        $match = $orderProcess->makeMatchOneByOne();
        $this->assertNotNull($match);


        // Check if event created is correct

        $eventFactory = $blockchain->getEventFactory();
        $eventFactory->populateLocal();
        /** @var BlockchainEvent[] $events */
        $events = $eventFactory->getEntities();

        $this->assertCount(1, $events);

        $event = end($events);

        $quantityRef = $event->getReference(BlockchainEventFactory::EVENT_QUANTITY)->refValue ?? null;
        $this->assertNotNull($quantityRef);
        $this->assertEquals('1', $quantityRef);

        $sourceAddress = $event->getJoinedEntities(BlockchainEventFactory::EVENT_SOURCE_ADDRESS);
        $this->assertNotNull($sourceAddress);

        /** @var KusamaAddress $source */
        $source = end($sourceAddress);
        $this->assertEquals(strtolower($this->firstAddress), $source->getAddress());

        $destinationAddress = $event->getJoinedEntities(BlockchainEventFactory::EVENT_DESTINATION_VERB);
        $this->assertNotNull($destinationAddress);

        /** @var KusamaAddress $destination */
        $destination = end($destinationAddress);
        $this->assertEquals(strtolower($this->secondAddress), $destination->getAddress());

        $specifier = $event->getSpecifier();
        $this->assertNotNull($specifier);

        $this->assertEquals("sn-".$this->snSell, $specifier->getDisplayStructure());


        // Check if balance is correct after balance building

        BalanceBuilder::flagAllForValidation($blockchain->getEventFactory());
        BalanceBuilder::buildBalance($blockchain->getEventFactory(), false, 1);

        // sender balance
        $senderBalance = DatagraphSource::getBalance($firstAddress, null, null);
        $contract = $event->getBlockchainContract();
        $senderTokenBalance = $senderBalance->getQuantityForContractToken($contract, $specifier);
        $this->assertEquals("0", $senderTokenBalance);

        // receiver balance
        $receiverBalance = DatagraphSource::getBalance($secondAddress, null, null);
        $contract = $event->getBlockchainContract();
        $receiverTokenBalance = $receiverBalance->getQuantityForContractToken($contract, $specifier);
        $this->assertEquals("1", $receiverTokenBalance);
    }



// Massive orders treatment need to be updated for non kusama orders


//    public function testKusamaMatch()
//    {
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
//
//        TestManager::initTestDatagraph();
//
//        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');
//
//        $this->makeKusamaMatchOrders();
//
//        $orderFactory = new BlockchainOrderFactory($blockchain);
//        $orderFactory->populateWithMatch();
//
//        $matchedOrders = $orderFactory->getEntities();
//
//        $this->assertNotEmpty($matchedOrders);
//
//        $matches = [];
//
//        foreach ($matchedOrders as $matchOrder){
//            $joined = $matchOrder->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);
//            if(!is_null($joined)){
//                $matches[] = $matchOrder;
//            }
//        }
//
//        $this->assertNotEmpty($matches);
//        /** @var BlockchainOrder $match */
//        $match = end($matches);
//
////        $isClose = $match->getReference(BlockchainOrderFactory::STATUS);
////        $this->assertNotNull($isClose);
////        $this->assertEquals(BlockchainOrderFactory::CLOSE, $isClose->refValue);
//
//        $remainingTotal = $match->getTotal();
//        $this->assertEquals(0, $remainingTotal);
//
//        $eventFactory = $blockchain->getEventFactory();
//        $eventFactory->populateLocal();
//        $events = $eventFactory->getEntities();
//
////        $this->assertCount(1, $events);
//
//
//        /** @var Entity[] $brothers */
//        $brothers = $match->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
//        $this->assertNotNull($brothers);
//
//        $brother = end($brothers);
//
//        $matchQuantity = $brother->getReference(BlockchainOrderFactory::MATCH_BUY_QUANTITY)->refValue;
//        $this->assertEquals($this->contractQuantity, $matchQuantity);
//
//        $matchKsm = $brother->getReference(BlockchainOrderFactory::MATCH_SELL_QUANTITY)->refValue;
//        $this->assertEquals($this->ksmQuantity, $matchKsm);
//
//        $matchedOrders = $match->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);
//        $this->assertNotNull($matchedOrders);
//
//        /** @var BlockchainOrder $orderMatched */
//        $orderMatched = end($matchedOrders);
//
//        $remainingTotal = $orderMatched->getTotal();
//        $this->assertEquals(0, $remainingTotal);
//
//    }
//
//
//
//    public function testViewOrders()
//    {
//        ini_set('display_errors', 1);
//        ini_set('display_startup_errors', 1);
//        error_reporting(E_ALL);
//
//        TestManager::initTestDatagraph();
//
//        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');
//
//        $this->makeKusamaMatchOrders();
//
//        $orderFactory = new BlockchainOrderFactory($blockchain);
//        $orderFactory->populateWithMatch();
//
//        /** @var BlockchainOrder[] $matches */
//        $matches = $orderFactory->getEntities();
//
//        $orderProcess = $blockchain->getOrderProcess();
//
//        $view = $orderProcess->makeViewFromOrders($matches, true);
//
//        $this->assertIsArray($view);
//        $this->assertNotEmpty($view);
//
//        $firstOrder = $view[0];
//
//        $this->assertEquals(strtolower($this->firstAddress), $firstOrder['source']);
//        $this->assertEquals(BlockchainOrderFactory::CLOSE, $firstOrder['status']);
//        $this->assertEquals($blockchain->getMainCurrencyTicker(), $firstOrder['contract_buy']);
//
//        $matchedOrder = $view[1];
//
//        $this->assertEquals(strtolower($this->secondAddress), $matchedOrder['source']);
//        $this->assertEquals(BlockchainOrderFactory::CLOSE, $matchedOrder['status']);
//        $this->assertEquals("BUY", $matchedOrder['order_type']);
//        $this->assertIsArray($matchedOrder['match_with']);
//
//        $matchWith = $matchedOrder['match_with'][0];
//
//        $this->assertArrayHasKey('token_sell', $matchWith);
//        $this->assertEquals("sn-" . $this->snSell, $matchWith['token_sell']);
//        $this->assertArrayHasKey('source', $matchWith);
//        $this->assertEquals(strtolower($this->firstAddress), $matchWith['source']);
//    }




    private function makeKusamaMatchOrders()
    {

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $factory = new BlockchainOrderFactory($blockchain);

        $addressFactory = $blockchain->getAddressFactory();

        $firstAddress = $addressFactory->get($this->firstAddress, true);
        $secondAddress = $addressFactory->get($this->secondAddress, true);

        $this->createOrder($blockchain, 'contractSell', $this->snSell, $this->contractQuantity, 'KSM', null, $this->ksmQuantity, 'txTestSell', 11122233, $factory, $firstAddress);
        $this->createOrder($blockchain, 'KSM', null, $this->ksmQuantity, 'contractSell', $this->snSell, $this->contractQuantity, "txTestBuy", 1112223345, $factory, $secondAddress, $firstAddress);

        $orderProcess = $blockchain->getOrderProcess();
        return $orderProcess->getAllMatches();
    }



    private function createOrder(Blockchain $blockchain, string $contractToSell, $snToSell, int $sellAmount, string $contractToBuy, $snToBuy, int $buyAmount, string $txHash, int $timestamp, BlockchainOrderFactory $blockchainOrderFactory, BlockchainAddress $source, BlockchainAddress $buyDestination = null, $updateBalance = true): BlockchainOrder
    {

        $buyContract = $blockchain->getContractFactory()::getContract($contractToBuy,true, RmrkContractStandard::getEntity());
        $sellContract = $blockchain->getContractFactory()::getContract($contractToSell,true, RmrkContractStandard::getEntity());

        if(!is_null($snToBuy)){
            $snToBuy = RmrkContractStandard::init(['sn' => $snToBuy]);
        }

        if(!is_null($snToSell)){
            $snToSell = RmrkContractStandard::init(['sn' => $snToSell]);

            if($updateBalance){
                $firstBalance = DatagraphSource::getBalance($source, null, null);
                $firstBalance->addContractToken($sellContract, $snToSell, $this->contractQuantity);
                $firstBalance->saveToDatagraph();
            }
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