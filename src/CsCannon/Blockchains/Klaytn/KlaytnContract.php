<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Klaytn;



use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\Ethereum\KlaytnContractFactory;
use SandraCore\ForeignEntityAdapter;

class KlaytnContract extends BlockchainContract
{

    protected static $isa = 'klaytnContract';

    protected static  $className = 'CsCannon\Blockchains\Ethereum\KlaytnContract' ;






    public function getBlockchain():Blockchain
    {
        return KlaytnBlockchain::getStatic();
    }

    public function getAbi(){

        $abiEntity = $this->getBrotherEntity(KlaytnContractFactory::ABI_VERB,
            KlaytnContractFactory::ABI_TARGET);

        if (!$abiEntity) return null ;

        $abi =  $abiEntity->getStorage();

        return $abi ;


    }

    public function setAbi($abi){

        $abiEntity = $this->setBrotherEntity(KlaytnContractFactory::ABI_VERB,
            KlaytnContractFactory::ABI_TARGET,null);

        $abiEntity->setStorage($abi);

        return $abi ;


    }







}