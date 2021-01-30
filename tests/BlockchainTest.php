<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;





final class BlockchainTest extends TestCase
{

    public function testBlockchain()

    {


      $exploreTx = \CsCannon\Blockchains\Klaytn\KlaytnBlockchain::getNetworkData('cypress','explorerTx');

      $this->assertEquals('https://scope.klaytn.com/tx/',$exploreTx);


    }

    public function suspendtestMainSourceCurrencyTicker()

    {

        $arrayOfCompatiblesChains = \CsCannon\BlockchainRouting::getSupportedBlockchains();

        foreach ($arrayOfCompatiblesChains as $blockchain){

            $blockchain->getMainCurrencyTicker();

            $this->assertNotEquals('NULL',$blockchain->getMainCurrencyTicker());

        }




    }











}
