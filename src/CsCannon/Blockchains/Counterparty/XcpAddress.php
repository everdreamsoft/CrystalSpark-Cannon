<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;



use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;
use CsCannon\SandraManager;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntityAdapter;

class XcpAddress extends BitcoinAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    public static  $className = 'CsCannon\Blockchains\XcpAddress' ;



    public function getBalance():Balance{

        $system = SandraManager::getSandra();
       // dd($this->getAddress());

        $collectionFactory = new AssetCollectionFactory($system);
        $collectionFactory->populateLocal();



        $xchainAdapter = new XchainOnBcy($system,$collectionFactory);
        $balance = $xchainAdapter->getBalance($this,1000,0);

        $this->balance = $balance ;




       return $balance;


        //return $reponse;





}





    public  function getBlockchain():Blockchain{

        return new XcpBlockchain() ;


    }








}