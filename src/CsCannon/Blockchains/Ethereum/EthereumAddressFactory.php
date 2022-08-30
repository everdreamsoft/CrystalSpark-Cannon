<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;





use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;

class EthereumAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'ethAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;

    public function get($address,$autoCreate = false):BlockchainAddress{

        $address = strtolower($address);
        return parent::get($address,$autoCreate);

    }

    public static function getBlockchain(){

        return EthereumBlockchain::class ;


    }







}
