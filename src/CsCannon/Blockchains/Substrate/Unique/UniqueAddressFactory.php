<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;

class UniqueAddressFactory extends SubstrateAddressFactory
{

    public static $isa = 'uniqueAddress';
    public static $file = 'uniqueAddressFile';
    protected static $className = UniqueAddress::class ;

    public function get($address,$autoCreate = false):BlockchainAddress{

        $address = strtolower($address);
        return parent::get($address,$autoCreate);

    }

    public static function getBlockchain(){

        return UniqueBlockchain::class ;
    }
}
?>