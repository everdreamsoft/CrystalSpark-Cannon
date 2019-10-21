<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Blockchains\BlockchainBlock;
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

        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get($testContract,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());
        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory(new \CsCannon\Blockchains\Ethereum\EthereumBlockchain());
        $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(\CsCannon\Blockchains\BlockchainBlockFactory::INDEX_SHORTNAME,1); //first block

        $Erc721 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
        $erc20 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC20::init();
        $Erc721->setTokenId('111');

        $eventFactory = new \CsCannon\Blockchains\Ethereum\EthereumEventFactory();
        $event = $eventFactory->create(new \CsCannon\Blockchains\Ethereum\EthereumBlockchain(),
            $addressEntity,
            $addressEntity,
            $contract,
            self::FIRSTTX,
            "123343555",
            $currentBlock,
                            $tokenId = $Erc721


        );

        $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainEvent::class,$event);





        $event2 = $eventFactory->create(new \CsCannon\Blockchains\Ethereum\EthereumBlockchain(),
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

        $eventFactory = new \CsCannon\Blockchains\Ethereum\EthereumEventFactory();
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







}
