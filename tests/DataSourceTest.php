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
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Orb;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721 ;





final class DataSourceTest extends TestCase
{

    public const COLLECTION_NAME = "My First Collection2" ;
    public const COLLECTION_CODE = "MyFirstCollection2" ;
    public const COLLECTION_CONTRACT = "myContract2" ;

    public function testXCP()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        \CsCannon\Tests\TestManager::initTestDatagraph();

        //No test yet
        $this->assertEquals(1,1);








    }










}


