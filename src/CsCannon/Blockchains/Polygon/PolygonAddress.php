<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */


namespace CsCannon\Blockchains\Polygon;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\DataSource\DatagraphSource;

class PolygonAddress extends BlockchainAddress
{

    protected static $className = 'CsCannon\Blockchains\Polygon\PolygonAddress';
    protected static $defaultDataSource = 'CsCannon\Blockchains\DataSource\DatagraphSource';

    public function getBlockchain(): Blockchain
    {
        return PolygonBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
        return new DatagraphSource();
    }
    
}
