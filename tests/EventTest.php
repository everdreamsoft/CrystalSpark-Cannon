<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainOrder;
use PHPUnit\Framework\TestCase;





final class EventTest extends TestCase
{

    const FIRSTTX = "FooTX1";
    const ERC20_QUANTITY = "200";

    public function testSaveEvent()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        \CsCannon\Tests\TestManager::initTestDatagraph();


        $testAddress = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;
        $testContract = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressFactoryControl = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressEntity = $addressFactory->get($testAddress,1);

        $contractFactory = new \CsCannon\Blockchains\Klaytn\KlaytnContractFactory();
        $contract = $contractFactory->get($testContract,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());
        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory(new \CsCannon\Blockchains\Klaytn\KlaytnBlockchain());
        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(\CsCannon\Blockchains\BlockchainBlockFactory::INDEX_SHORTNAME,1); //first block

        $Erc721 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
        $erc20 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC20::init();
        $Erc721->setTokenId('111');

        $eventFactory = new \CsCannon\Blockchains\Klaytn\KlaytnEventFactory();
        $event = $eventFactory->create(new \CsCannon\Blockchains\Klaytn\KlaytnBlockchain(),
            $addressEntity,
            $addressEntity,
            $contract,
            self::FIRSTTX,
            "123343555",
            $currentBlock,
                            $tokenId = $Erc721


        );

        $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainEvent::class,$event);





        $event2 = $eventFactory->create(new \CsCannon\Blockchains\Klaytn\KlaytnBlockchain(),
            $addressEntity,
            $addressEntity,
            $contractFactory->get('anotherFooContract',true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC20::init()),
            'fooTX EWRC 20',
            "123343555",
            $currentBlock,
            $tokenId = $erc20,
            $quantity = self::ERC20_QUANTITY

        );





    }

    public function testGetEvent()
    {

        $eventFactory = new \CsCannon\Blockchains\Klaytn\KlaytnEventFactory();
        $eventFactory->populateLocal();
        $eventList = $eventFactory->getEntities();

        foreach ($eventList ? $eventList : array() as $event){
            /** @var \CsCannon\Blockchains\BlockchainEvent $event */

           $specifier = $event->getSpecifier();
           $eventContract = $event->getBlockchainContract();

           $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainEvent::class,$event);
           $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainAddress::class,$event->getSourceAddress());
           $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainAddress::class,$event->getDestinationAddress());

           // \CsCannon\Tests\TestManager::registerDataStructure();


        }

        $output = $eventFactory->display()->html()->return();



        $this->assertEquals(self::FIRSTTX,$output[0][\CsCannon\Blockchains\BlockchainEvent::DISPLAY_TXID]);
        $this->assertEquals(self::ERC20_QUANTITY,$output[1][\CsCannon\Blockchains\BlockchainEvent::DISPLAY_QUANTITY]);



    }



    public function testSaveOrder()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        \CsCannon\Tests\TestManager::initTestDatagraph();

        $blockhain = \CsCannon\BlockchainRouting::getBlockchainFromName('kusama');

        $testAddress = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;
        $testContract = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;

        $blockchainOrderFactory = new \CsCannon\Blockchains\BlockchainOrderFactory(new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain());
        $blockchainOrderFactory->populateLocal();

        $myContract = $blockhain->getContractFactory()::getContract('0xToken1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());
        $tokenBuy = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => '0000000000000BUY']);
        $tokenSale = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => '0000000000000SELL']);

        $addressFactory = $blockhain->getAddressFactory();
        $addressFactoryControl = $blockhain->getAddressFactory();
        $addressEntity = $addressFactory->get($testAddress,1);

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($blockhain);

        /** @var BlockchainBlock $currentBlock */
        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(\CsCannon\Blockchains\BlockchainBlockFactory::INDEX_SHORTNAME,1); //first block


            $event = $blockchainOrderFactory->createOrder($blockhain,
            $addressEntity,
                $myContract,
                $myContract,
            1,
            2,
            2,
            "testTx",
            111111,
            $currentBlock,
                $tokenBuy,
                $tokenSale

        );

        //Cold plug
        $blockchainOrderFactory = new \CsCannon\Blockchains\BlockchainOrderFactory(new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain());
        $blockchainOrderFactory->populateLocal();
        $events = $blockchainOrderFactory->getEntities();
        $event = end($events);
        /** @var BlockchainOrder $event */

            $this->assertEquals($event->getTxId(),'testTx');
            $this->assertEquals(1,$event->getContractToBuyQuantity());
            $this->assertEquals(2,$event->getContractToSellQuantity());
            $this->assertEquals($myContract->getId(),$event->getContractToBuy()->getId());
            $this->assertEquals($myContract->getId(),$event->getContractToSell()->getId());


            $this->assertEquals("sn-0000000000000BUY",$event->getTokenBuy()->getDisplayStructure());
            $this->assertEquals("sn-0000000000000SELL",$event->getTokenSell()->getDisplayStructure());



         //$this->assertEquals($testAddress,$event->getBuyDestination()->getAddress());

//

//

//
//        $contractFactory = new \CsCannon\Blockchains\Klaytn\KlaytnContractFactory();
//        $contract = $contractFactory->get($testContract,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());
//        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory(new \CsCannon\Blockchains\Klaytn\KlaytnBlockchain());
//        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(\CsCannon\Blockchains\BlockchainBlockFactory::INDEX_SHORTNAME,1); //first block
//
//        $Erc721_buy =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
//        $Erc721_sell =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
//        $erc20 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC20::init();
//        $Erc721_buy->setTokenId('2');
//        $Erc721_sell->setTokenId('1');
//
//        $eventFactory = new \CsCannon\Blockchains\Klaytn\KlaytnEventFactory();
//        $event = $eventFactory->createOrder(new \CsCannon\Blockchains\Klaytn\KlaytnBlockchain(),
//            $addressEntity,
//            $contract,
//            $contract,
//            1,
//            1,
//            1,
//            "testTx",
//            111111,
//            $currentBlock,
//            $Erc721_buy,
//            $Erc721_sell
//
//
//
//        );
//
//        $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainEvent::class,$event);
//       $factory = new \CsCannon\Blockchains\Klaytn\KlaytnEventFactory();
//       $factory->populateLocal();
//
//      $event =  $factory->first(Blockchain::$txidConceptName,"testTx");
//
//      $getSellPrice = $event->getBrotherReference(BlockchainEventFactory::ORDER_SELL_CONTRACT,null,
//          BlockchainEventFactory::SELL_PRICE);
//
//      $sellPriceOfOne = end($getSellPrice);
//      print_r($getSellPrice);





    }



    public function testRmrkProcessOrder()
    {



    }



}
