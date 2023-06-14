<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainContract;

class OrdinalContract extends BlockchainContract
{
    protected static $isa = 'ordContract';
    protected static $className = 'CsCannon\Blockchains\Ordinal\OrdinalContract';

    public function getBlockchain(): Blockchain
    {
        return OrdinalBlockchain::getStatic();
    }

    public function getAbi()
    {
        $abiEntity = $this->getBrotherEntity(OrdinalContractFactory::ABI_VERB,
            OrdinalContractFactory::ABI_TARGET);
        if (!$abiEntity) return null;
        $abi = $abiEntity->getStorage();
        return $abi;
    }

    public function setAbi($abi)
    {
        $abiEntity = $this->setBrotherEntity(OrdinalContractFactory::ABI_VERB,
            OrdinalContractFactory::ABI_TARGET, null);
        $abiEntity->setStorage($abi);
        return $abi;
    }

}
