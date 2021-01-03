<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\FirstOasis;




use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\BlockchainContract;



class FirstOasisContract extends BlockchainContract
{

    protected static $isa = null ;

    protected static  $className = 'CsCannon\Blockchains\FirstOasis\FirstOasisContract' ;






    public function getBlockchain():Blockchain
    {
        return FirstOasisBlockchain::getStatic();
    }

    public function getAbi(){

        $abiEntity = $this->getBrotherEntity(FirstOasisContractFactory::ABI_VERB,
            FirstOasiscContractFactory::ABI_TARGET);

        if (!$abiEntity) return null ;

        $abi =  $abiEntity->getStorage();

        return $abi ;


    }

    public function setAbi($abi){

        $abiEntity = $this->setBrotherEntity(FirstOasisContractFactory::ABI_VERB,
            FirstOasisContractFactory::ABI_TARGET,null);

        $abiEntity->setStorage($abi);

        return $abi ;


    }







}