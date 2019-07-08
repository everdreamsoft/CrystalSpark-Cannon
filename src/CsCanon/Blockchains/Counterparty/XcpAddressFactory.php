<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;




use CsCanon\Blockchains\Bitcoin\BtcAddressFactory;

class XcpAddressFactory extends BtcAddressFactory
{

    public static $isa = 'btcAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCanon\Blockchains\Counterparty\XcpAddress' ;








}