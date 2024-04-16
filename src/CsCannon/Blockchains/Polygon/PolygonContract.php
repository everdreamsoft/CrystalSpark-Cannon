<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */

namespace CsCannon\Blockchains\Polygon;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainContract;

class PolygonContract extends BlockchainContract
{

    protected static $isa = 'polygonContract';
    protected static $className = 'CsCannon\Blockchains\Polygon\PolygonContract';

    public function getBlockchain(): Blockchain
    {
        return PolygonBlockchain::getStatic();
    }

    public function getAbi()
    {
        $abiEntity = $this->getBrotherEntity(PolygonContractFactory::ABI_VERB,
            PolygonContractFactory::ABI_TARGET);
        if (!$abiEntity) return null;
        $abi = $abiEntity->getStorage();
        return $abi;
    }

    public function setAbi($abi)
    {
        $abiEntity = $this->setBrotherEntity(PolygonContractFactory::ABI_VERB,
            PolygonContractFactory::ABI_TARGET, null);
        $abiEntity->setStorage($abi);
        return $abi;

    }

}
