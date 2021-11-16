<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;

class BinanceAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'bscAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Binance\BinanceAddress' ;

    public function get($address,$autoCreate = false):BlockchainAddress{
        $address = strtolower($address);
        return parent::get($address,$autoCreate);
    }

    public static function getBlockchain(){
        return BinanceBlockchain::class ;
    }

}