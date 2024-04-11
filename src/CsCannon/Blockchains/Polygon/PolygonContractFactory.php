<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */

namespace CsCannon\Blockchains\Polygon;

use CsCannon\Blockchains\BlockchainContractFactory;

class PolygonContractFactory extends BlockchainContractFactory
{

    public static $isa = 'polygonContract';
    const ABI_VERB = 'has';
    const ABI_TARGET = 'abi';

    protected static $className = 'CsCannon\Blockchains\Polygon\PolygonContract';

    public function __construct()
    {
        $this->blockchain = PolygonBlockchain::class;
        return parent::__construct();
    }

}
