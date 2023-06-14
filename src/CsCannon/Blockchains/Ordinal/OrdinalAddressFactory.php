<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;

class OrdinalAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'ordAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Ordinal\OrdinalAddress' ;

    public function get($address,$autoCreate = false):BlockchainAddress{
        $address = strtolower($address);
        return parent::get($address,$autoCreate);
    }

    public static function getBlockchain(){
        return OrdinalBlockchain::class ;
    }

}
