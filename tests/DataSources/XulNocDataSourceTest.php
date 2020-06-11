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

class XulNocDataSourceTest extends DataSourceAbstract
{
    private $contractToTest ;

    public function loadTestCases() {

        $addressFactory = new \CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticAddressFactory();

        $address = $addressFactory->get('0x7f7EED1fcBb2C2cf64d055eED1Ee051DD649C8e7');
        $this->addressToBeChecked[] = $address ;


       $maticContractFactory = new \CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticContractFactory();
        //we should have 2 tokens of this contract blockchain cutties
       $contractSog = $maticContractFactory->get('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe');

       $this->contractToTest[] = $contractSog;




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

        $address->setDataSource(new \CsCannon\Blockchains\DataSource\XulNocDataSource());


        $balance = $address->getBalanceForContract($this->contractToTest);

        //we should have equal contract in the balance as the number of requested contracts
        $this->assertCount(count($this->contractToTest),$balance->getContractMap());




    }




}