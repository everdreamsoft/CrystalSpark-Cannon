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

class EthereumContractFactory extends BlockchainContractFactory
{

    public static $isa = 'ethContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';




    protected static $className = 'CsCannon\Blockchains\Ethereum\EthereumContract' ;


    public function __construct()
    {
        $this->blockchain = EthereumBlockchain::class ;
        return parent::__construct();

    }

    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {

        //in ethereum we transform it lowercase
        $identifier = strtolower($identifier);
        return parent::get($identifier,$autoCreate, $contractStandard);


    }












}