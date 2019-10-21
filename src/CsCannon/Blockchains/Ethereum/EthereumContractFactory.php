<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;







use CsCannon\Blockchains\BlockchainContractFactory;

class EthereumContractFactory extends BlockchainContractFactory
{

    public static $isa = 'ethContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';


    protected static $className = 'CsCannon\Blockchains\Ethereum\EthereumContract' ;












}