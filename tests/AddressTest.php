<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;





final class AddressTest extends TestCase
{

    public function testAddressTestXcp()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();


        $testAddress = \CsCannon\Tests\TestManager::XCP_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressFactoryControl = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressEntity = $addressFactory->get($testAddress);

        /** @var \CsCannon\Blockchains\Counterparty\XcpAddressFactory $addressFactory */


       $this->assertInstanceOf(\CsCannon\Blockchains\Counterparty\XcpAddress::class,$addressEntity,
           "blockchain Router didnt return a counterparty instance for $testAddress");

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
            "Xcp Address not stored on the datagraph");

        //check if the stored address is a counterparty
        $this->assertInstanceOf(\CsCannon\Blockchains\Counterparty\XcpAddress::class,
            reset($localAddressListControl),"locally saved address is not an XcpAddress object");


    }

    public function testGetBalance()
    {

        $testAddress = \CsCannon\Tests\TestManager::XCP_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressFactoryControl = CsCannon\BlockchainRouting::getAddressFactory($testAddress);
        $addressEntity = $addressFactory->get($testAddress);


        $balanceObject = $addressEntity->getBalance();

        $this->assertInstanceOf(\CsCannon\Balance::class,
            $balanceObject,"Get Balance not returning an object balance");

        //does balance contain contracts
        $contractMap = $balanceObject->getContractMap();
        $theFirstContract = reset($contractMap);

        $this->assertInstanceOf(\CsCannon\Blockchains\Counterparty\XcpContract::class,
            $theFirstContract,"Balance doens't return counterparty contracts");


        /** @var \CsCannon\Blockchains\Blockchain $blockchain */
        $blockchain = $theFirstContract->getBlockchain();

        //Do we have a correct quantity for a contract token ?
        $counterpartyContractStandard =new \CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset(); //XCP has one standard
        $counterpartyContractStandard->getDisplayStructure();

        $theToken = $balanceObject->contracts[$blockchain::NAME][\CsCannon\Tests\TestManager::XCP_TOKEN_AVAIL];
        $this->assertEquals($theToken[$counterpartyContractStandard->getDisplayStructure()]['quantity'],2
            ,"Balance doens't return counterparty contracts");

    }







}
