<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;

class RmrkContractFactory extends SubstrateContractFactory
{

    public static $isa = 'rmrkContract';


    protected static $className = UniqueContract::class;

    public function __construct()
    {

        $return = parent::__construct();
        $this->blockchain = UniqueBlockchain::class;
        return $return ;
    }

    public function get($identifier, $autoCreate=false, BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {
        return parent::get($identifier, $autoCreate, $contractStandard);
    }
}