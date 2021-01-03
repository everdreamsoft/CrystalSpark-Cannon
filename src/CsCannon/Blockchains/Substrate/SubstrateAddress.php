<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;

class SubstrateAddress extends BlockchainAddress
{
    protected static  $className = 'Unique\Blockchains\Substrate\SubstrateAddress';
    public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return SubstrateBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
       return new \CsCannon\Blockchains\DataSource\DatagraphSource();
    }
}
