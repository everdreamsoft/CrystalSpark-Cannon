<?php

namespace DataSources;
require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload
require_once __DIR__ . '/DataSourceAbstract.php';

use CsCannon\Blockchains\Ethereum\DataSource\BlockDaemonDataSource;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\DataSource\AlchemyDataSource;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use DataSourceAbstract;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class AlchemyDataSourceTest extends DataSourceAbstract
{
    private array $contractToTest;
    private array $expectedTokens;
    private array $txToBeChecked;

    public function loadTestCases()
    {
        parent::loadTestCases();

        $ethereumContractFactory = new EthereumContractFactory();



        // CryptoKitties, 1 token OK
        $this->contractToTest[] = $ethereumContractFactory->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');
        $this->expectedTokens["0x06012c8cf97bead5deae237070f9587f8e7a266d"][] = "390158";

        // Gods unchained 3 tokens OK
        $this->contractToTest[] = $ethereumContractFactory->get('0x0e3a2a1f2146d86a604adc220b4967a898d7fe07');
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "61979547";
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "61979546";
        $this->expectedTokens["0x0e3a2a1f2146d86a604adc220b4967a898d7fe07"][] = "49808568";

        $this->txToBeChecked = [
            '0xff710d9de5e859e585b18e18285321ea21ff75757150ebe00d3101462ce5e94e' => [
                'src_address' => '0x152ae2469128b9fb3fbc297d04787301c0a1cb4e',
                'dst_address' => '0xb15d2895d0a893d8384baabc4c2614cc938a7ac8',
                'quantity' => 8000,
                'contract' => '0xadbbb02e20c44779e87f7ea90c47c9a7a8a93fee',
                'decimals' => '8' //BCY has 8 decimal
            ],
            '0x68395d8bce4548d805c003f6ad57e03d249c91e1d1822246421fe1979fc2f2f8' => [
                'src_address' => '0x5536C6AdB821c418D7f6230aa351E1B82B1F7Cc6',
                'dst_address' => '0x071c7bcc5e5bac0f2551cb8dd16a3a0a0baef341',
                'quantity' => 47000,
                'contract' => '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48',
                 'decimals' => '6' // USDC has 6 deceimals
            ],

        ];
    }
    /* Failed transaction
     *  '0xd6b4496575531167c8d434c51aac39cfa24bfbfaae98295ff44481b63175700f' => [ // a failed transaction
                'src_address' => 'x',
                'dst_address' => 'x',
                'quantity' => 47000,
                'contract' => 'x',
                 'decimals' => '6' // USDC has 6 deceimals
            ]
     */

    public function testGetBalanceForContract()
    {

        $this->loadTestCases();

        $address = $this->addressToBeChecked[0];
        $address->setDataSource(new AlchemyDataSource());
        $balance = $address->getBalanceForContract($this->contractToTest);

        $this->assertCount(count($this->contractToTest), $balance->getContractMap());

        $tokenBalance = $balance->getTokenBalance();

        foreach ($tokenBalance as $contractData) {
            $contract = $contractData["contract"] ?? null;
            $this->assertNotNull($contract);

            $this->assertArrayHasKey($contract, $this->expectedTokens);

            $tokenCount = 0;
            foreach ($contractData["tokens"] as $tokenData) {
                $this->assertEquals(1, $tokenData["quantity"]);
                $this->assertTrue(in_array($tokenData["tokenId"], $this->expectedTokens[$contract]));
                $tokenCount++;
            }
            $this->assertCount($tokenCount, $this->expectedTokens[$contract]);
            unset($this->expectedTokens[$contract]);
        }
        $this->assertEmpty($this->expectedTokens);
    }

    public function testGetTransactionDetails(){
        $this->loadTestCases();
        $erc20 = ERC20::init();
    foreach ($this->txToBeChecked as $txHash => $expectedData) {
        $details = AlchemyDataSource::getDecodedTransaction($txHash, AlchemyDataSource::NETWORK_ETH_MAIN, $erc20, $expectedData['decimals']);


        $this->assertNotNull($details);
        $this->assertEquals(strtolower($expectedData['src_address']), strtolower($details->src_address));
        $this->assertEquals(strtolower($expectedData['dst_address']), strtolower($details->dst_address)); 
        $this->assertEquals($expectedData['quantity'], $details->quantity);
        $this->assertEquals(strtolower($expectedData['contract']), strtolower($details->contract));
    }



    }

    public function testGetTransactionStatus(){
        $this->loadTestCases();
        $erc20 = ERC20::init();
        foreach ($this->txToBeChecked as $txHash => $expectedData) {
            $details = AlchemyDataSource::getTransactionStatus('eth',$txHash, AlchemyDataSource::NETWORK_ETH_MAIN);
            $details = \CsCannon\Blockchains\Polygon\DataSource\AlchemyDataSource::getTransactionStatus('eth',$txHash, AlchemyDataSource::NETWORK_ETH_MAIN);


            $this->assertEquals('true', $details);

        }



    }

}
