<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class BinanceAddress extends BlockchainAddress
{

    protected static $isa = 'bscAddress';
    protected static $file = 'bscAddressFile';
    protected static $className = 'CsCannon\Blockchains\Binance\BinanceAddress';
   
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain():Blockchain
    {
        return BinanceBlockchain::getStatic();
    }

    public function getDefaultDataSource():BlockchainDataSource
    {
        return new DatagraphSource();
    }

}
        
