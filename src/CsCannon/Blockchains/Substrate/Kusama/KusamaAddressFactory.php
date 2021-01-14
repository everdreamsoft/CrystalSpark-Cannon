<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;

class KusamaAddressFactory extends SubstrateAddressFactory
{

    public static $isa = 'KusamaAddress';
    public static $file = 'KusamaAddressFile';
    protected static $className = KusamaAddress::class ;

    public function get($address,$autoCreate = false):BlockchainAddress{

        $address = strtolower($address);
        return parent::get($address,$autoCreate);

    }

    public static function getBlockchain(){

        return KusamaBlockchain::class ;
    }
}
?>