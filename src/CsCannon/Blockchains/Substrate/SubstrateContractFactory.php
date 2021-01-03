<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;

class SubstrateContractFactory extends BlockchainContractFactory
{

    public static $isa = 'substrateContract';


    protected static $className = SubstrateContract::class;

    public function __construct()
    {
        $this->blockchain = SubstrateBlockchain::class;
        return parent::__construct();
    }

    public function get($identifier, $autoCreate=false, BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {
        return parent::get($identifier, $autoCreate, $contractStandard);
    }
}