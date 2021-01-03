<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Generic;





use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class GenericAddressFactory extends BlockchainAddressFactory
{

    public static $isa = null;
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Generic\GenericAddress' ;

    public static function getBlockchain(){

        return GenericBlockchain::class ;


    }







}