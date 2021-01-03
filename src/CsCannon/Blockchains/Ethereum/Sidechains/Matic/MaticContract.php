<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;




use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Ethereum\EthereumContract;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

class MaticContract extends EthereumContract
{

    protected static $isa = 'maticContract';

    protected static  $className = 'CsCannon\Blockchains\Ethereum\MaticContract' ;






    public function getBlockchain():Blockchain
    {
        return MaticBlockchain::getStatic();
    }

    public function getAbi(){

        $abiEntity = $this->getBrotherEntity(MaticContractFactory::ABI_VERB,
            MaticContractFactory::ABI_TARGET);

        if (!$abiEntity) return null ;

        $abi =  $abiEntity->getStorage();

        return $abi ;


    }

    public function setAbi($abi){

        $abiEntity = $this->setBrotherEntity(MaticContractFactory::ABI_VERB,
            MaticContractFactory::ABI_TARGET,null);

        $abiEntity->setStorage($abi);

        return $abi ;


    }







}