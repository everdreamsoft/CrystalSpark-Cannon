<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContract;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\SandraManager;
use PHPUnit\Framework\TestCase;





final class BlockchainRoutingTest extends TestCase
{

    public function testBlockchainRouting()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();


       $blockchains =  BlockchainRouting::getBlockchainsFromAddress(\CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS);

       //As for now we have 3 compoatible blockchains type ethereum
       $this->assertCount(3,$blockchains);

        $blockchains =  BlockchainRouting::getBlockchainsFromAddress(\CsCannon\Tests\TestManager::XCP_TEST_ADDRESS);
        $this->assertCount(1,$blockchains);

        $factories =  BlockchainRouting::getAddressFactoriesFromAddress(\CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS);
        $firstFactory = reset($factories);

        $this->assertInstanceOf(BlockchainAddressFactory::class,$firstFactory);
        $this->assertCount(3,$factories);




    }

    public function testGenenicContractGetBlockchain(){

        $countSupportedChain = 0 ;
       $supportedChainsArray = BlockchainRouting::getSupportedBlockchains() ;
        foreach ($supportedChainsArray as $blockchain){

            $countSupportedChain++ ;

            $cf = $blockchain->getContractFactory();
            $cf->get("myCOntractOn".$blockchain::NAME,true);

        }

        $genericContractFactory = new \CsCannon\Blockchains\Generic\GenericContractFactory();
        $genericContractFactory->populateLocal();

        $i = 0 ;
        foreach ($genericContractFactory->getEntities() as $contract){

            $arrayOfBlockchainFromContract = BlockchainRouting::getBlockchainFromGenericContract($contract);
            $firstBlockchainFromContract = reset($arrayOfBlockchainFromContract);

            //the first supported chain
            $actualBlockchainToTest = $supportedChainsArray[$i];
            $this->assertInstanceOf(get_class($firstBlockchainFromContract),$actualBlockchainToTest);
            $i++;

        }

    }

    public function testGenenicAddressGetBlockchain(){

        $countSupportedChain = 0 ;
        $supportedChainsArray = BlockchainRouting::getSupportedBlockchains() ;
        foreach ($supportedChainsArray as $blockchain){

            $countSupportedChain++ ;

            $cf = $blockchain->getAddressFactory();
            $cf->get("addressOn ".$blockchain::NAME,true);

        }

        $genericAddressFactory = new \CsCannon\Blockchains\Generic\GenericContractFactory();
        $genericAddressFactory->populateLocal();

        $i = 0 ;
        foreach ($genericAddressFactory->getEntities() as $address){

            $arrayOfBlockchainFromContract = BlockchainRouting::getBlockchainFromGenericContract($address);
            $firstBlockchainFromContract = reset($arrayOfBlockchainFromContract);

            //the first supported chain
            $actualBlockchainToTest = $supportedChainsArray[$i];
            $this->assertInstanceOf(get_class($firstBlockchainFromContract),$actualBlockchainToTest);
            $i++;

        }

    }

    public function testSearch(){

        $contractString = '0xd346d304ea1837053452357c2066a4701de9a04b';

        EthereumContractFactory::getContract($contractString,true);

        $search = BlockchainRouting::searchConceptFromString($contractString, SandraManager::getSandra());
        $result1 = reset($search);

        $this->assertInstanceOf(EthereumContract::class,$result1);
        $this->assertEquals($contractString,$result1->getId());

        $addressString = '1AyyAr2u2aQr7uHvMueL7pHzYCvPdVQhvx';

        XcpAddressFactory::getAddress($addressString,true);

        $search = BlockchainRouting::searchConceptFromString($addressString, SandraManager::getSandra());
        $result1 = reset($search);

        $this->assertInstanceOf(\CsCannon\Blockchains\Counterparty\XcpAddress::class,$result1);
        $this->assertEquals($addressString,$result1->getAddress());


    }

    public function testCustomBlockchain(){

       $customBlockchain = new CustomBlockchain();

       BlockchainRouting::addBlockchainSupport($customBlockchain);

       $routedBlockchain = BlockchainRouting::getBlockchainFromName($customBlockchain::NAME);

       $this->assertEquals($customBlockchain,$routedBlockchain);


    }







}

class CustomBlockchain extends Blockchain {

    protected $name = 'substrate';
    const NAME = 'substrate';
    protected $nameShort = 'substrate';
    private static $staticBlockchain;


    public function __construct()
    {
        $this->addressFactory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();
        $this->contractFactory = new EthereumContractFactory();
        $this->eventFactory = new EthereumEventFactory();
    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new CustomBlockchain();
        }

        return self::$staticBlockchain ;
    }


}
