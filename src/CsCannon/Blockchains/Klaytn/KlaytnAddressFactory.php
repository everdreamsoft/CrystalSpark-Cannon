<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Klaytn;





use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class KlaytnAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'klaytnAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Klaytn\KlaytnAddress' ;

    public static function getBlockchain(){

        return KlaytnBlockchain::class ;


    }







}