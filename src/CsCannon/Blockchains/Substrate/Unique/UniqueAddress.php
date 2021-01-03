<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;

class UniqueAddress extends BlockchainAddress
{
    protected static  $className = UniqueAddress::class;
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return UniqueBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
       return new \CsCannon\Blockchains\DataSource\DatagraphSource();
    }
}
