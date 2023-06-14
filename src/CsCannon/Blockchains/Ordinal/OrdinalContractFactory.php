<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;

class OrdinalContractFactory extends BlockchainContractFactory
{

    public static $isa = 'ordContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';

    protected static $className = 'CsCannon\Blockchains\Ordinal\OrdinalContract' ;

    public function __construct()
    {
        $this->blockchain = OrdinalBlockchain::class ;
        return parent::__construct();
    }

    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {
        $identifier = strtolower($identifier);
        return parent::get($identifier,$autoCreate, $contractStandard);
    }












}
