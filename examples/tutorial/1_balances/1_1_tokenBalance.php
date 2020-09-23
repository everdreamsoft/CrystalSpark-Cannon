<?php


/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 14:42
 */

use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// In use while we test. Comment this line if you are using this project stand alone
require_once __DIR__ . '/../../../vendor/autoload.php';

//require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html'; // Don't forget to configure your database in config.php


    echoTitle("Loading token balances");

    $ethBlockchain = new EthereumBlockchain();

    /* In this example we are going to query token balance from a datasource (OpenSeaDataSource)
    then we are going to save this balance into local datagraph. Then we are going to query balance from our local
    datagraph. The result should be the same.


    */


    //the address to query balance
    $testEthAddress = '0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79';

    echoSubTitle("Loading balance from OpenSea ");
    echoExplanations("We are loading balance from wallet : $testEthAddress using OpenSea as datasource");
    


    $ethereumAddressFactory = new EthereumAddressFactory();
    $myTestEthereumAddress = $ethereumAddressFactory->get($testEthAddress, true); //get an address object from the factory

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaDataSource());

    $balance = $myTestEthereumAddress->getBalance(); //this will return a balance object
    $tokenArray = $balance->getTokenBalanceArray();

    echoArray($tokenArray);

    $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory(new \CsCannon\Blockchains\Ethereum\EthereumBlockchain());
    $currentBlock = $blockchainBlockFactory->getOrCreateFromRef(\CsCannon\Blockchains\BlockchainBlockFactory::INDEX_SHORTNAME,1); //first block
    $balance->saveToDatagraph();

    echoExplanations("We save the balance we got from OpenSea to our local database (datagraph)");
    echoCode(' $balance->saveToDatagraph();');

    echoTitle("Read balance from local Datagraph");

    $ethereumAddressFactory = new EthereumAddressFactory();
    $myTestEthereumAddress = $ethereumAddressFactory->get($testEthAddress, true); //get an address object from the factory

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\DataSource\DatagraphSource()); //we are defining our local database as datasource;

    $balance = $myTestEthereumAddress->getBalance(); //this will return a balance object
    $tokenArray = $balance->getTokenBalanceArray();

    echoExplanations("The result from getting balance from our local database");
    echoArray($tokenArray);















/*
$myToken = ERC721::init(10);

$eventFactory = new \CsCannon\Blockchains\Ethereum\EthereumEventFactory();
$eventFactory->create($ethBlockchain,$myEthereumAddress,$myEthereumAddress,$myContract,
    '0x1d270fe0d386ec038804165158addec0f20983a9543ee52e5667617b89ae7a37',1600435494,$myBlock,$myToken,1);
*/

