<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Ethereum;





use App\Blockchains\BlockchainAddressFactory;
use App\Blockchains\BlockchainContractFactory;

class EthereumContractFactory extends BlockchainContractFactory
{

    public static $isa = 'ethAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'App\Blockchains\Ethereum\EthereumContract' ;












}