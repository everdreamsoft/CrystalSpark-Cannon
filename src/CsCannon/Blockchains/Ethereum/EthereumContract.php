<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;

class EthereumContract extends BlockchainContract
{
    protected static $isa = 'ethContract';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumContract' ;

    public function getBlockchain():Blockchain
    {
        return EthereumBlockchain::getStatic();
    }

    public function getAbi(){

        $abiEntity = $this->getBrotherEntity(EthereumContractFactory::ABI_VERB,
            EthereumContractFactory::ABI_TARGET);

        if (!$abiEntity) return null ;

        $abi =  $abiEntity->getStorage();

        return $abi ;


    }

    public function setAbi($abi){

        $abiEntity = $this->setBrotherEntity(EthereumContractFactory::ABI_VERB,
            EthereumContractFactory::ABI_TARGET,null);

        $abiEntity->setStorage($abi);

        return $abi ;


    }







}
