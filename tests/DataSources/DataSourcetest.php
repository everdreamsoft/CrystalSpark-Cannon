<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 10.12.19
 * Time: 10:17
 */

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use PHPUnit\Framework\TestCase;

class DataSourceTest extends TestCase
{

    /**
     * @var BlockchainAddress[]
     */
    protected $addressToBeChecked = array() ;


    public function loadTestCases() {


        $addressFactory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();
        $address = $addressFactory->get('0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79');
        $this->addressToBeChecked[] = $address ;


    }



    public function testDataSource() {

        \CsCannon\Tests\TestManager::initTestDatagraph();

        $this->loadTestCases();

        $addressFactory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();

        foreach ($this->addressToBeChecked as $address){

            $dataSource = $address->getDataSource();

            $this->dataSourceCompliance($dataSource,$address);



        }

        $this->assertEquals(1,1);


    }

    public function dataSourceCompliance(BlockchainDataSource $dataSource,
                                         BlockchainAddress $address) {


        $balance = $dataSource::getBalance($address,100,0);
        $this->assertGreaterThan(2,count($balance->getContractMap()));

        $importer = new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter();

        $events = $dataSource::getEvents(null,null,null,$address);
        $events = $events->getEntities();

        //we check if datasource gives correct data
        $event = end($events);

        //do we have a correct timestamp ?
        $this->assertGreaterThan('1200048724',$event->get(BlockchainImporter::TRACKER_BLOCKTIME),
            "timestamp is not bigger than jan 2018");

        $this->assertGreaterThan('1200048724',$event->get(BlockchainImporter::TRACKER_BLOCKTIME),
            "timestamp is not bigger than jan 2018");



        //importEvents
        $blockchainImporter = new \CsCannon\Blockchains\Ethereum\EthereumImporter($address->system);
        $blockchainImporter->getEvents(null,null,null,null,$address);




    }





}