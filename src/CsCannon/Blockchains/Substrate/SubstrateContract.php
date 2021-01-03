<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;

class SubstrateContract extends BlockchainContract
{

    protected static $isa = 'substrateContract';
    protected static  $className = SubstrateContract::class ;

    public function getBlockchain():Blockchain
    {
        return SubstrateBlockchain::getStatic();
    }
}