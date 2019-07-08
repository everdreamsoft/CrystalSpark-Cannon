<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;






use CsCanon\Blockchains\BlockchainTokenFactory;

class XcpTokenFactory extends BlockchainTokenFactory
{

    public static $isa = 'xcpToken';
    public static $file = 'xcpTokenFile';
    protected static $className = 'CsCanon\Blockchains\Counterparty\XcpToken' ;








}