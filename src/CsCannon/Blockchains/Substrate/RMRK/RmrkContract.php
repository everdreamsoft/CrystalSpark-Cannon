<?php

namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use SandraCore\ForeignEntityAdapter;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContract;

class RmrkContract extends SubstrateContract
{

    protected static $isa = 'rmrkContract';
    protected static  $className = RmrkContract::class;

    public function getBlockchain():Blockchain
    {
        //this is no good
        return KusamaBlockchain::getStatic();
    }
}