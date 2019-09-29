<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;



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
use CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\EthereumAddress;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Ethereum\MaticBlockchain;
use CsCannon\Ethereum\EthereumToken;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class MaticAddress extends EthereumAddress //update relevant parent
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticAddress' ; //Update relevant path





    public function getDefaultDataSource(): BlockchainDataSource
    {


        return  new BlockscoutAPI();
    }


    public function getBlockchain(): Blockchain
    {
        return MaticBlockchain::getStatic();
    }




}