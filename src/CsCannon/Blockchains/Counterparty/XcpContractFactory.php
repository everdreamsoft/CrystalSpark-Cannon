<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;





use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;

class XcpContractFactory extends BlockchainContractFactory
{

    public static $isa = 'xcpContract';
    public static $file = 'blockchainContractFile';
    protected static $className = 'CsCannon\Blockchains\Counterparty\XcpContract' ;


    public function __construct()
    {
        $this->blockchain = XcpBlockchain::class ;
        return parent::__construct();

    }


    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {

        //for counterparty we force counterparty contract standard
        $contractStandard = CounterpartyAsset::init();

        return parent::get($identifier,$autoCreate, $contractStandard);


    }

    public function resolveMetaData (){




    }









}