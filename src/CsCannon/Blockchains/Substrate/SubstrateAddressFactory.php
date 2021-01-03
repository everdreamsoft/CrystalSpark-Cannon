<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;

class SubstrateAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'substrateAddress';
    public static $file = 'substrateAddressFile';
    protected static $className = SubstrateAddress::class ;



    public function get($address,$autoCreate = false):BlockchainAddress{

        $address = strtolower($address);
        return parent::get($address,$autoCreate);

    }

    public static function getBlockchain(){

        return SubstrateBlockchain::class ;
    }
}
?>