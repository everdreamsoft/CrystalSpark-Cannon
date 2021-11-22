<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Binance\Interfaces\ERC721;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

use CsCannon\Blockchains\DataSource\DatagraphSource;

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
        
