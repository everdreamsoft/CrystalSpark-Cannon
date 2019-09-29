<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;



use CsCannon\Asset;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;
use CsCannon\SandraManager;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntityAdapter;

class XcpAddress extends BitcoinAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    public static  $className = 'CsCannon\Blockchains\XcpAddress' ;
    protected static $defaultDataSource = 'CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy' ;









    public  function getBlockchain():Blockchain{

        return new XcpBlockchain() ;


    }


    public function getDefaultDataSource(): BlockchainDataSource
    {

        /** @var BlockchainDataSource $defaultDataSource */
        $newClass = new self::$defaultDataSource() ;



       return $newClass ;
    }
}