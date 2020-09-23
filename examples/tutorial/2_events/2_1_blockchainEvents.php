<?php


/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 14:42
 */

use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpEventFactory;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Generic\GenericAddressFactory;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Generic\GenericEventFactory;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



//require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html'; // Don't forget to configure your database in config.php


    echoTitle("Blockchain Events");

    echoExplanations("Everytime you refresh this page it will add a new random transaction to your datagraph");

    $ethBlockchain = new EthereumBlockchain();
    $eventFactory = $ethBlockchain->getEventFactory();


    echoTitle("Blockchain Events");
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
        $blockObject = BlockchainBlockFactory::getOrCreateBlockWithId($count,$blockchain);
        $txId = "0xdummyTx".$count ;

        //This is where we save a new transaction
        $eventFactory = $blockchain->getEventFactory();
        $eventFactory->create($blockchain,
            $dummySourceAddress,
            $dummyDestinationAddress,
            $dummyContract,$txId,time(),$blockObject);


    }
    createRandomTransaction();

    //Now that we created a random transaction let's create a function that displays transactions

    function displayTransactionFromFactory(BlockchainEventFactory $factory ){

        //we are going to load display only 25 transaction per factory
       $transactions = $factory->populateLocal(25,0,'DESC'); //limit 25 offset 0 and DESC order (from last to first)
        $tableHTML = '' ;
        $fullLine = '';

        foreach ($transactions as $transaction){

            /** @var \CsCannon\Blockchains\BlockchainEvent $transaction */
            $lineHtml = buildTd($transaction->getBlockchainName()[0]);
            $lineHtml .= buildTd($transaction->getTxId());
            $lineHtml .= buildTd($transaction->getSourceAddress()->getAddress());
            $lineHtml .= buildTd($transaction->getDestinationAddress()->getAddress());
            $lineHtml .= buildTd($transaction->getBlockchainContract()->getId());

            $fullLine .= buildTr($lineHtml);

        }

        echoHTMLTable($fullLine);


    }

    echoTitle("Transaction by blockchains");

    echoSubTitle("show anychain TX");
    echoCode('
    //Now that we created a random transaction let\'s create a function that displays transactions
    function displayTransactionFromFactory(BlockchainEventFactory $factory ){

        //we are going to load display only 25 transaction per factory
       $transactions = $factory->populateLocal(25,0,"DESC"); //limit 25 offset 0 and DESC order (from last to first)
        $tableHTML = "" ;
        $fullLine = "";

        foreach ($transactions as $transaction){

            /** @var \CsCannon\Blockchains\BlockchainEvent $transaction */
            $lineHtml = buildTd($transaction->getBlockchainName()[0]);
            $lineHtml .= buildTd($transaction->getTxId());
            $lineHtml .= buildTd($transaction->getSourceAddress()->getAddress());
            $lineHtml .= buildTd($transaction->getDestinationAddress()->getAddress());
            $lineHtml .= buildTd($transaction->getBlockchainContract()->getId());

            $fullLine .= buildTr($lineHtml);

        }

        echoHTMLTable($fullLine);


    }');
    echoCode("displayTransactionFromFactory(new GenericEventFactory());");
    displayTransactionFromFactory(new GenericEventFactory());

    echoSubTitle("show ethereum TX");
    displayTransactionFromFactory(new EthereumEventFactory());

    echoSubTitle("show Counterparty TX");
    displayTransactionFromFactory(new XcpEventFactory());


    //filter section
    echoTitle("Filter transactions");



    //we are displaying transaction from 0xDummySourceAddress0 on any chain
    $addressToSearch = '0xDummySourceAddress0';
    echoSubTitle("Show transaction on any chain with $addressToSearch as sender");
    $transactionFactory = new GenericEventFactory();
    $transactionFactory->filterBySender(GenericAddressFactory::getAddress($addressToSearch));
    displayTransactionFromFactory($transactionFactory);

    //we are displaying transaction to 0xDummySourceAddress0 on ethereum having
    $addressToSearch = '0xDummySourceAddress0';
    $filterContract = '0xMyDummyContract';
    echoSubTitle("Show transaction on ethereum with $addressToSearch as receiver and $filterContract as contract");
    $transactionFactory = new EthereumEventFactory();
    $transactionFactory->filterByReceiver(EthereumAddressFactory::getAddress($addressToSearch));
    $transactionFactory->filterByContract(EthereumContractFactory::getContract($filterContract));
    displayTransactionFromFactory($transactionFactory);





require_once '../viewFooter.html';













