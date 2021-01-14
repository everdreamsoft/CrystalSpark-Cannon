<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;

class KusamaAddress extends BlockchainAddress
{
    protected static  $className = KusamaAddress::class;
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return KusamaBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
       return new \CsCannon\Blockchains\DataSource\DatagraphSource();
    }
}
