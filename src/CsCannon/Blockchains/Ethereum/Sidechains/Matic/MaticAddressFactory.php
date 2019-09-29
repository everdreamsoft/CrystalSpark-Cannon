<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;





use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\MaticBlockchain;

class MaticAddressFactory extends EthereumAddressFactory
{

    public static $isa = 'ethAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticAddress' ;

    public static function getBlockchain(){

        return MaticBlockchain::class ;


    }







}