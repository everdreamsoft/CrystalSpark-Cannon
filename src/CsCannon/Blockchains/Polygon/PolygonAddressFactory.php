<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */

namespace CsCannon\Blockchains\Polygon;


use CsCannon\Blockchains\BlockchainAddressFactory;

class PolygonAddressFactory extends BlockchainAddressFactory
{

    public static $isa = 'polygonAddress';
    public static $file = 'blockchainAddressFile';
    protected static $className = 'CsCannon\Blockchains\Polygon\PolygonAddress';

    public static function getBlockchain()
    {
        return PolygonBlockchain::class;
    }

}
