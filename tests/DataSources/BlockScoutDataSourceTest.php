<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 10.12.19
 * Time: 10:17
 */

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;
include_once 'DataSourceAbstract.php';



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class BlockScoutDataSourceTest extends DataSourceAbstract
{
    private $contractToTest ;

    public function loadTestCases() {


       parent::loadTestCases();

       $ethereumContractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        //we should have 2 tokens of this contract blockchain cutties
       $contractCuties = $ethereumContractFactory->get('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe');

        //we should have 2 tokens of this contract
        $contract = $ethereumContractFactory->get('0xf5b0a3efb8e8e4c201e2a935f110eaaf3ffecb8d');
        $contractBcy = $ethereumContractFactory->get('0xAdbBB02E20C44779e87F7eA90C47c9A7a8A93fee');
        $contractBcy->setDivisibility(8);


       $this->contractToTest[] = $contractCuties;
       $this->contractToTest[] = $contract;
        $this->contractToTest[] = $contractBcy;



    }


    public function __construct($name = null, array $data = [], $dataName = '')
    {

        $myEthereum = new \CsCannon\Blockchains\Ethereum\EthereumBlockchain();

        parent::__construct($name, $data, $dataName);
    }

    public function testGetBalanceForContract()
    {

        $this->loadTestCases();

        $address = $this->addressToBeChecked[0];
        $address->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI());


        $balance = $address->getBalanceForContract($this->contractToTest);

        //we should have equal contract in the balance as the number of requested contracts
        $this->assertCount(count($this->contractToTest),$balance->getContractMap());


    }

    public function testERC20()
    {

        $this->loadTestCases();

        $address = $this->addressToBeChecked[0];
        $address->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI());


        $balance = $address->getBalanceForContract([$this->contractToTest[2]]);


        //we should have equal contract in the balance as the number of requested contracts
        $this->assertCount(count($this->contractToTest),$balance->getContractMap());


    }


}