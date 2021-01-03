<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;






use CsCannon\Blockchains\BlockchainTokenFactory;

class XcpTokenFactory extends BlockchainTokenFactory
{

    public static $isa = 'xcpToken';
    public static $file = 'xcpTokenFile';
    protected static $className = 'CsCannon\Blockchains\Counterparty\XcpToken' ;








}