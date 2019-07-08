<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Ethereum;





use App\Blockchains\BlockchainAddressFactory;

class EthereumAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'ethAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'App\Blockchains\Ethereum\EthereumAddress' ;









}