<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;





final class EthAddressTest extends TestCase
{

    public function testAddressTestXcp()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();


        $testAddress = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressFactoryControl = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressEntity = $addressFactory->get($testAddress);

        /** @var \CsCannon\Blockchains\Counterparty\XcpAddressFactory $addressFactory */


       $this->assertInstanceOf(\CsCannon\Blockchains\Ethereum\EthereumAddress::class,$addressEntity,
           "blockchain Router didnt return a Ethereum instance for $testAddress");

       //this method shouldn't save the address
        $localAddressListControl = $addressFactoryControl->populateLocal();


        $this->assertCount(0,$localAddressListControl,"Get address stored the address on the datagraph
        but shouldn't");

        //now we store the address on the datagraph
        $addressEntity = $addressFactory->get($testAddress,1);

        //and we try to retreive it
        $addressFactory->populateLocal();
        $localAddressListControl = $addressFactoryControl->populateLocal();
        $this->assertCount(1,$localAddressListControl,
            "Eth Address not stored on the datagraph");

        //check if the stored address is a counterparty
        $this->assertInstanceOf(\CsCannon\Blockchains\Ethereum\EthereumAddress::class,
            reset($localAddressListControl),"locally saved address is not an Ethereum Address object");

        \CsCannon\Tests\TestManager::registerDataStructure();


    }

    public function testGetBalance()
    {

        $testAddress = \CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressFactoryControl = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressEntity = $addressFactory->get($testAddress);


        $balanceObject = $addressEntity->getBalance();

        $this->assertInstanceOf(\CsCannon\Balance::class,
            $balanceObject,"Get Balance not returning an object balance");

        //does balance contain contracts
        $contractMap = $balanceObject->getContractMap();
        $theFirstContract = reset($contractMap);

        $this->assertInstanceOf(\CsCannon\Blockchains\Ethereum\EthereumContract::class,
            $theFirstContract,"Balance doens't return Ethereum contracts");


        /** @var \CsCannon\Blockchains\Blockchain $blockchain */
        $blockchain = $theFirstContract->getBlockchain();

        //Do we have a correct quantity for a contract token ?
        $contractSandard =new \CsCannon\Blockchains\Ethereum\Interfaces\ERC721();
        $contractSandard->tokenId = \CsCannon\Tests\TestManager::ETHEREUM_TOKEN_ID ;
        $contractSandard->getDisplayStructure();

        $theToken = $balanceObject->contracts[$blockchain::NAME][\CsCannon\Tests\TestManager::ETHEREUM_TOKEN_AVAIL];
        $this->assertEquals($theToken[$contractSandard->getDisplayStructure()]['quantity'],1
            ,"Balance doens't return counterparty contracts");

        \CsCannon\Tests\TestManager::registerDataStructure();

    }







}
