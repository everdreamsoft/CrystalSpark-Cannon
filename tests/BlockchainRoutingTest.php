<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\BlockchainAddressFactory;
use PHPUnit\Framework\TestCase;





final class BlockchainRoutingTest extends TestCase
{

    public function testBlockchainRouting()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();


       $blockchains =  BlockchainRouting::getBlockchainsFromAddress(\CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS);

       //As for now we have 3 compoatible blockchains type ethereum
       $this->assertCount(3,$blockchains);

        $blockchains =  BlockchainRouting::getBlockchainsFromAddress(\CsCannon\Tests\TestManager::XCP_TEST_ADDRESS);
        $this->assertCount(1,$blockchains);

        $factories =  BlockchainRouting::getAddressFactoriesFromAddress(\CsCannon\Tests\TestManager::ETHEREUM_TEST_ADDRESS);
        $firstFactory = reset($factories);

        $this->assertInstanceOf(BlockchainAddressFactory::class,$firstFactory);
        $this->assertCount(3,$factories);


    }







}
