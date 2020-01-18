<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\FirstOasis;







use CsCannon\Blockchains\BlockchainContractFactory;

class FirstOasisContractFactory extends BlockchainContractFactory
{

    public function __construct()
    {
        $this->blockchain = FirstOasisBlockchain::getStatic();
        return parent::__construct();

    }

    public static $isa = 'firstOasisContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';


    protected static $className = 'CsCannon\Blockchains\FirstOasis\FirstOasisContract' ;












}