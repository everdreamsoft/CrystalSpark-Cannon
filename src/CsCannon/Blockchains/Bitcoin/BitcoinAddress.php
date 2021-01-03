<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Bitcoin;


use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use SandraCore\System;

class BitcoinAddress extends BlockchainAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    protected $address ;


    public function getBlockchain(): Blockchain
    {
        return new BtcBlockchain(); ;
    }




    public function createForeign(){







    }


    public function getDefaultDataSource(): BlockchainDataSource
    {
        return null ;
    }
}