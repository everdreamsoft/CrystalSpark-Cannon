<?php

namespace DataSources;
require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload
require_once __DIR__ . '/DataSourceAbstract.php';

use CsCannon\Blockchains\Ethereum\DataSource\BlockDaemonDataSource;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\DataSource\AlchemyDataSource;
use DataSourceAbstract;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


class AlchemyDataSourceTest extends DataSourceAbstract
{
    private array $contractToTest;
    private array $expectedTokens;

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
    }

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

}
