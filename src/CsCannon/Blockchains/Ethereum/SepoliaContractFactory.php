<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;

class SepoliaContractFactory extends BlockchainContractFactory
{
    public static $isa = 'sepoliaContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';

    protected static $className = 'CsCannon\Blockchains\Ethereum\SepoliaContract' ;

    public function __construct()
    {
        $this->blockchain = SepoliaBlockchain::class ;
        return parent::__construct();
    }

    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {
        //in Sepolia we transform it lowercase
        $identifier = strtolower($identifier);
        return parent::get($identifier,$autoCreate, $contractStandard);
    }
}