<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.09.20
 * Time: 18:23
 */

use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



//require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html';

    $testEthAddress = '0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79';

    echoSubTitle("Work in progress");
    echoExplanations("On our tutorial 1_1_tokenBalance we learned how to get token balance from an address
    but this is not very sexy. In this tutorial we are going to look how we can retreive assets from a token contract
    ");


    echoExplanations("the balance of our example address 0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79 shows
     seven ERC-721 tokens within four different contracts. But for this example let's focus on CryptoCuties contract
    0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe
    ");


    $myTestEthereumAddress = EthereumAddressFactory::getAddress($testEthAddress,true); //get an address object from the factory
    $contract = EthereumContractFactory::getContract('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe',true);

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaDataSource());
    $balance = $myTestEthereumAddress->getBalanceForContract(array($contract)); //note this time we do get balance for a single contract
    //note we are passing an array of contracts as parameters. Opensea datasource has at the time of writing issue with querying multiple contracts.



    echoExplanations("The token balance for this specific contract is as follow :
    ");

    echoArray($balance->getTokenBalanceArray());

    echoExplanations("If the balance didn't change we should have 2 ERC721 tokens with tokenIds 47225 30450
    ");




