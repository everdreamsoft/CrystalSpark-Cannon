<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Klaytn;



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
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Ethereum\EthereumToken;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class KlaytnAddress extends BlockchainAddress
{


    protected static  $className = 'CsCannon\Blockchains\Klaytn\KlaytnAddress' ;
    protected static $defaultDataSource = 'CsCannon\Blockchains\Klaytn\DataSource\OpenSeaImporter' ;







    public function getBlockchain(): Blockchain
    {
        return KlaytnBlockchain::getStatic();
    }



    public function getDefaultDataSource(): BlockchainDataSource
    {


       return  new OpenSeaImporter();
    }
}