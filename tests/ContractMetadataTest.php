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
use CsCannon\OrbFactory;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;





final class ContractMetadataTest extends TestCase
{

    public $scenarioContract =  null ;
    public $scenarioCollection =  null ;



    public function testContractMetadata(){





    }




}


