<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;

class BinanceContractFactory extends BlockchainContractFactory
{

    public static $isa = 'bscContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';

    protected static $className = 'CsCannon\Blockchains\Binance\BinanceContract' ;

    public function __construct()
    {
        $this->blockchain = BinanceBlockchain::class ;
        return parent::__construct();
    }

    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {
        //in binance we transform it lowercase
        $identifier = strtolower($identifier);
        return parent::get($identifier,$autoCreate, $contractStandard);
    }












}