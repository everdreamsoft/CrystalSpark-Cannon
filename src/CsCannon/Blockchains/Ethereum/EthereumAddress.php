<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Ethereum\DataSource\AlchemyDataSource;
use CsCannon\Blockchains\Ethereum\DataSource\BlockDaemonDataSource;

class EthereumAddress extends BlockchainAddress
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;
    public static $defaultDataSource = 'CsCannon\Blockchains\Ethereum\DataSource\AlchemyDataSource';


    public function getBlockchain(): Blockchain
    {
        return EthereumBlockchain::getStatic();
    }

    public function getDefaultDataSource(): BlockchainDataSource
    {
       //return new DatagraphSource();
//       return  new OpenSeaImporter();
       return new AlchemyDataSource();
    }
}
