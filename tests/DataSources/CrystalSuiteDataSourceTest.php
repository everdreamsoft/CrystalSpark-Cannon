<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 10.12.19
 * Time: 10:17
 */

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload





ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class CrystalSuiteDataSourceTest extends DataSourceAbstract
{
    private $contractToTest ;
    private $expectedTokens;

    public function loadTestCases() {


       parent::loadTestCases();

       $ethereumContractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        //we should have 2 tokens of this contract blockchain cutties
        // CryptoKitties, 1 token OK
        $this->contractToTest[] = $ethereumContractFactory->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');
        $this->expectedTokens["0x06012c8cf97bead5deae237070f9587f8e7a266d"][] = "390158";

        // Gods unchained 3 tokens OK
        $this->contractToTest[] = $ethereumContractFactory->get('0x0e3a2a1f2146d86a604adc220b4967a898d7fe07');
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "61979547";
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "61979546";
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "49808568";


    }


    public function __construct($name = null, array $data = [], $dataName = '')
    {



        parent::__construct($name, $data, $dataName);
    }

    public function testGetBalanceForContract()
    {

        $this->loadTestCases();

        $address = $this->addressToBeChecked[0];
        $address->setDataSource(new \CsCannon\Blockchains\DataSource\CrystalSuiteDataSource());


        $balance = $address->getBalanceForContract($this->contractToTest);

        //we should have equal contract in the balance as the number of requested contracts
        $this->assertCount(count($this->contractToTest),$balance->getContractMap());







    }


}
