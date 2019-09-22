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

class XcpContractFactory extends BlockchainContractFactory
{

    public static $isa = 'xcpContract';
    public static $file = 'blockchainContractFile';
    protected static $className = 'CsCannon\Blockchains\Counterparty\XcpContract' ;


    // On counterparty a contract is the token itself
    public function get($tokenName,$autoCreate = false):?BlockchainContract{


        $entityToken = $this->getOrCreateFromRef(BlockchainContractFactory::MAIN_IDENTIFIER, $tokenName);



        return $entityToken ;

    }

    public function resolveMetaData (){




    }









}