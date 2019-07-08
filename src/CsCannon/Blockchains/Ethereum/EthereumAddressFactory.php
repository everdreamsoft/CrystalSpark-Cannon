<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;





use CsCannon\Blockchains\BlockchainAddressFactory;

class EthereumAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'ethAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;

    public static function getBlockchain(){

        return EthereumBlockchain::class ;


    }







}