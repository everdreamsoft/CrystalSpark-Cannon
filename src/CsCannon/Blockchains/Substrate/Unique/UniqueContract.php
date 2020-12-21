<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContract;

class UniqueContract extends SubstrateContract
{

    protected static $isa = 'uniqueContract';
    protected static  $className = UniqueContract::class;

    public function getBlockchain():Blockchain
    {
        return UniqueBlockchain::getStatic();
    }
}