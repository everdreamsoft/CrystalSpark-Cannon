<?php

use CsCannon\BlockchainRouting;
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

        $this->createOrder('contractSell', '00000SELL', 5, 'buyContract', '00000BUY', 3, 'txTestSell', 11122233, $factory);
        $this->createOrder('buyContract', '00000BUY', 10, 'contractSell', '00000SELL', 5, "txTestBuy", 1112223345, $factory);

        $orderFactory = new BlockchainOrderFactory($blockchain);
        $orderFactory->populateLocal();

        $orders = $orderFactory->getAllEntitiesOnChain();

        print_r(count($orders));
        $this->assertNotEmpty($orders);



//        $orderProcess = $blockchain->getOrderProcess();
//        $matches = $orderProcess->getAllMatches();
//        $this->assertNotEmpty($matches);
    }


    private function createOrder(string $contractToSell, string $snToSell, int $sellAmount, string $contractToBuy, string $snToBuy, int $buyAmount, string $txHash, int $timestamp, BlockchainOrderFactory $blockchainOrderFactory): BlockchainOrder
    {


        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $testAddress = TestManager::ETHEREUM_TEST_ADDRESS;

//        $blockchainOrderFactory = new BlockchainOrderFactory($blockchain);
//        $blockchainOrderFactory->populateLocal();

        $buyContract = $blockchain->getContractFactory()::getContract($contractToBuy,true, RmrkContractStandard::getEntity());
        $sellContract = $blockchain->getContractFactory()::getContract($contractToSell,true, RmrkContractStandard::getEntity());
        $tokenBuy = RmrkContractStandard::init(['sn' => $snToBuy]);
        $tokenSale = RmrkContractStandard::init(['sn' => $snToSell]);

        $addressFactory = $blockchain->getAddressFactory();
        $addressEntity = $addressFactory->get($testAddress,1);

        $blockchainBlockFactory = new BlockchainBlockFactory($blockchain);

        /** @var BlockchainBlock $currentBlock */
        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME,123456);


        return $blockchainOrderFactory->createOrder($blockchain,
            $addressEntity,
            $buyContract,
            $sellContract,
            $buyAmount,
            $sellAmount,
            $buyAmount * $sellAmount,
            $txHash,
            $timestamp,
            $currentBlock,
            $tokenBuy,
            $tokenSale

        );

    }


}