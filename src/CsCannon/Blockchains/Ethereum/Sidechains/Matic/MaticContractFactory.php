<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

class MaticContractFactory extends EthereumContractFactory
{

    public static $isa = 'maticContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';




    protected static $className = 'CsCannon\Blockchains\Ethereum\MaticContract' ;


    public function __construct()
    {
        $this->blockchain = MaticBlockchain::class ;
        return parent::__construct();

    }














}