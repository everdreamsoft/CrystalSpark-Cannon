<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Klaytn;




use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Counterparty\DataSource\DatagraphSource;


class GenericAddress extends BlockchainAddress
{


    protected static  $className = 'CsCannon\Blockchains\Generic\GenericAddress' ;
    protected static $defaultDataSource = 'CsCannon\Blockchains\Counterparty\DataSource\DatagraphSource' ;







    public function getBlockchain(): Blockchain
    {
        return KlaytnBlockchain::getStatic();
    }



    public function getDefaultDataSource(): BlockchainDataSource
    {


       return  new DatagraphSource();
    }
}