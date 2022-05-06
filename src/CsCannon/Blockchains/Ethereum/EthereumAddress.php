<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Ethereum\EthereumToken;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class EthereumAddress extends BlockchainAddress
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;
    public static $defaultDataSource = 'CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter' ;

    //public static $defaultDataSource = '\CsCannon\Blockchains\DataSource\DatagraphSource';






    public function getBlockchain(): Blockchain
    {
        return EthereumBlockchain::getStatic();
    }



    public function getDefaultDataSource(): BlockchainDataSource
    {

       // return new DatagraphSource();
       return  new OpenSeaImporter();
    }
}
