<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\FirstOasis;




use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;


class FirstOasisAddress extends BlockchainAddress
{


    protected static  $className = 'CsCannon\Blockchains\FirstOasis\FirstOasisAddress' ;
    protected static $defaultDataSource = 'CsCannon\Blockchains\DataSource\DatagraphSource' ;







    public function getBlockchain(): Blockchain
    {
        return KlaytnBlockchain::getStatic();
    }



    public function getDefaultDataSource(): BlockchainDataSource
    {


       return  new DatagraphSource();
    }
}