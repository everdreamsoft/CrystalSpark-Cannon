<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;

class WestendAddressFactory extends SubstrateAddressFactory
{

    public static $isa = 'westendAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = WestendAddress::class ;

    public function get($address,$autoCreate = false):BlockchainAddress{

        $address = strtolower($address);
        return parent::get($address,$autoCreate);

    }

    public static function getBlockchain(){

        return WestendBlockchain::class ;
    }
}
?>