<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\FirstOasis;





use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class FirstOasisAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'firstOasisAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\FirstOasis\FirstOasisAddress' ;

    public static function getBlockchain(){

        return KlaytnBlockchain::class ;


    }







}