<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;




use CsCannon\Blockchains\Bitcoin\BtcAddressFactory;

class XcpAddressFactory extends BtcAddressFactory
{

    public static $isa = 'btcAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Counterparty\XcpAddress' ;

    public static function getBlockchain(){

        return XcpBlockchain::class ;


    }








}