<?php


/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 14:42
 */

use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpEventFactory;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Generic\GenericEventFactory;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html'; // Don't forget to configure your database in config.php


    echoTitle("Blockchain Events");

    echoExplanations("Everytime you refresh this page it will add a new random transaction to your datagraph");

    $ethBlockchain = new EthereumBlockchain();
    $eventFactory = $ethBlockchain->getEventFactory();

    //we set a function to add blockchain event in our datagraph
    function createRandomTransaction(){

        //This will return an array of supported blockchains
        $blockchainArray = \CsCannon\BlockchainRouting::getSupportedBlockchains();
        $randomBlockchain = $blockchainArray[array_rand($blockchainArray)];


        $blockchain = $randomBlockchain;
        $addressFactory = $blockchain->getAddressFactory();
        $contractFactory = new $blockchain->contractFactory ;
        // we are counting the number of ethereum address just to be able to increment 0xDummySourceAddress1...2
        $count = $addressFactory->countEntitiesOnRequest();

        //we create a dummy data, source and destination address contract, txid, block
        $dummySourceAddress = $addressFactory::getAddress("0xDummySourceAddress".$count,1);
        $dummyDestinationAddress = $addressFactory::getAddress("0xDummyDestinationAddress".$count,1);
        $dummyContract = $contractFactory::getContract("0xMyDummyContract",true, ERC721::init());
        $blockObject = \CsCannon\Blockchains\BlockchainBlockFactory::getOrCreateBlockWithId($count,$blockchain);
        $txId = "0xdummyTx".$count ;


        $eventFactory = $blockchain->getEventFactory();
        $eventFactory->create($blockchain,
            $dummySourceAddress,
            $dummyDestinationAddress,
            $dummyContract,$txId,time(),$blockObject);


    }
    createRandomTransaction();

    //Now that we created a random transaction let's create a function that displays transactions

    function displayTransactionFromFactory(BlockchainEventFactory $factory ){

        //we are going to load display only 10 transaction per factory
       $transactions = $factory->populateLocal(100,0,'DESC'); //limit 10 offset 0 and DESC order (from last to first)

        foreach ($transactions as $transaction){

            /** @var \CsCannon\Blockchains\BlockchainEvent $transaction */
            echo(" Blockchain : ".$transaction->getBlockchainName()[0].PHP_EOL);
            echo(" Source : ".$transaction->getSourceAddress()->getAddress().PHP_EOL);
            echo(" Destination : ".$transaction->getSourceAddress()->getAddress().PHP_EOL);
            echo"<BR>";

        }


    }

    echoSubTitle("show anychain TX");
    displayTransactionFromFactory(new GenericEventFactory());

    echoSubTitle("show ethereum TX");
    displayTransactionFromFactory(new EthereumEventFactory());

    echoSubTitle("show Counterparty TX");
    displayTransactionFromFactory(new XcpEventFactory());



















