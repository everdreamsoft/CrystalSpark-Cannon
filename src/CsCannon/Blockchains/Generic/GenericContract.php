<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Generic;




use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;


class GenericContract extends BlockchainContract
{

    protected static $isa = null ;

    protected static  $className = 'CsCannon\Blockchains\Generic\GenericContract' ;



    public function getBlockchain():Blockchain
    {
        $triples = $this->subjectConcept->tripletArray;
        if (isset($triples[$this->system->systemConcept->get(BlockchainContractFactory::ON_BLOCKCHAIN_VERB)])) {
            $blockchainCOnceptId = $triples[$this->system->systemConcept->get(BlockchainContractFactory::ON_BLOCKCHAIN_VERB)];
            $blockchainCOnceptId = reset($blockchainCOnceptId);
            $blockchainShortname = $this->system->systemConcept->getSCS($blockchainCOnceptId);
            $blockchain = BlockchainRouting::getBlockchainFromName($blockchainShortname);
            if (!is_null($blockchain))
                return $blockchain ;

        }
        return GenericBlockchain::getStatic();
    }

    public function getAbi(){

        $abiEntity = $this->getBrotherEntity(GenericContractFactory::ABI_VERB,
            GenericContractFactory::ABI_TARGET);

        if (!$abiEntity) return null ;

        $abi =  $abiEntity->getStorage();

        return $abi ;


    }

    public function setAbi($abi){

        $abiEntity = $this->setBrotherEntity(GenericContractFactory::ABI_VERB,
            GenericContractFactory::ABI_TARGET,null);

        $abiEntity->setStorage($abi);

        return $abi ;


    }







}