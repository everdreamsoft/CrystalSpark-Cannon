<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 10.12.19
 * Time: 10:17
 */

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload
require_once __DIR__ . '/DataSourceAbstract.php';


use PHPUnit\Framework\TestCase;




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class OpenSeaDataSourceTest extends DataSourceAbstract
{
    // Deprecated Datasource

    private $contractToTest ;

    public function loadTestCases() {


       parent::loadTestCases();


       $ethereumContractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        //we should have 2 tokens of this contract blockchain cutties
       $contractCuties = $ethereumContractFactory->get('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe');

        //we should have 2 tokens of this contract
        $contract = $ethereumContractFactory->get('0xf5b0a3efb8e8e4c201e2a935f110eaaf3ffecb8d');


       $this->contractToTest[] = $contractCuties;
       $this->contractToTest[] = $contract;



    }


    public function __construct($name = null, array $data = [], $dataName = '')
    {

        $myEthereum = new \CsCannon\Blockchains\Ethereum\EthereumBlockchain();

        parent::__construct($name, $data, $dataName);
    }

//    public function testGetBalanceForContract()
//    {
//
//        $this->loadTestCases();
//
//        $address = $this->addressToBeChecked[0];
//        $address->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter());
//
//
//        $balance = $address->getBalanceForContract($this->contractToTest);
//
//        //we should have equal contract in the balance as the number of requested contracts
//        $this->assertCount(count($this->contractToTest),$balance->getContractMap());
//
//
//
//
//
//
//
//    }


}