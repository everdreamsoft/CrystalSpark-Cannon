<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Bitcoin;


use CsCanon\Blockchains\BlockchainAddress;
use SandraCore\System;

class BitcoinAddress extends BlockchainAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    protected $address ;




    public function getBalance()
    {
        // TODO: Implement getBalance() method.
    }

    public function createForeign(){







    }


}