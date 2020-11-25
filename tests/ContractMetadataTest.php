<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Asset;
use CsCannon\AssetSolvers\BooSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Orb;
use CsCannon\OrbFactory;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;





final class ContractMetadataTest extends TestCase
{

    public $scenarioContract =  null ;
    public $scenarioCollection =  null ;



    public function testContractMetadata(){

        \CsCannon\Tests\TestManager::initTestDatagraph();

        $xcp = 'BITCRYSTALS';
        $contract = XcpContractFactory::getContract($xcp,true);
        $contract->metadata->getDecimals();
        $this->assertNull( $contract->metadata->getDecimals());
        $contract->setDataSource(new \CsCannon\Blockchains\Counterparty\DataSource\XchainDataSource());

        $metadata = $contract->metadata->refreshData();
        $this->assertEquals(8,$metadata->getDecimals());
        $this->assertFalse(boolval($metadata->isMutableSupply()));


        //check datastore
        $contract = EthereumContractFactory::getContract($xcp,true);
        //We have an issue with hotplug and simple get
        $xcpCOntractF = new XcpContractFactory();
        $xcpCOntractF->populateLocal();
        $contract = $xcpCOntractF->last(BlockchainContractFactory::MAIN_IDENTIFIER,$xcp);
        $this->assertEquals(8,$contract->metadata->getDecimals());
        $this->assertFalse(boolval($contract->metadata->isMutableSupply()));
        $this->assertEquals(10000000000000000,$contract->metadata->getTotalSupply());

    }




}


