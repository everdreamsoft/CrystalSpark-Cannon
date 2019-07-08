<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Bitcoin;


use App\Blockchains\BlockchainAddressFactory;

class BtcAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';




}