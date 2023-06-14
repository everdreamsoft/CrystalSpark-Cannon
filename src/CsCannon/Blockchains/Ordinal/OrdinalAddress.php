<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Blockchains\Ordinals\OrdinalBlockchain;

class OrdinalAddress extends BlockchainAddress
{

    protected static $isa = 'btcAddress';
    protected static $file = 'btcAddressFile';
    protected static $className = 'CsCannon\Blockchains\Ordinal\OrdinalAddress';
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return OrdinalBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
        return new DatagraphSource();
    }

}

