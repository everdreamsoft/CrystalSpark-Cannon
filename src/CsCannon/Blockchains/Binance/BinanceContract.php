<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;

class BinanceContract extends BlockchainContract
{
    protected static $isa = 'bscContract';
    protected static  $className = 'CsCannon\Blockchains\Binance\BinanceContract' ;

    public function getBlockchain():Blockchain
    {
        return BinanceBlockchain::getStatic();
    }

    public function getAbi(){
        $abiEntity = $this->getBrotherEntity(BinanceContractFactory::ABI_VERB,
            BinanceContractFactory::ABI_TARGET);
        if (!$abiEntity) return null ;
        $abi =  $abiEntity->getStorage();
        return $abi ;
    }

    public function setAbi($abi){
        $abiEntity = $this->setBrotherEntity(BinanceContractFactory::ABI_VERB,
            BinanceContractFactory::ABI_TARGET,null);
        $abiEntity->setStorage($abi);
        return $abi ;
    }

}