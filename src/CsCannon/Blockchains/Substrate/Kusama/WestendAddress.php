<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;

class WestendAddress extends BlockchainAddress
{
    protected static  $className = WestendAddress::class;
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return WestendBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
       return new \CsCannon\Blockchains\DataSource\DatagraphSource();
    }
}
