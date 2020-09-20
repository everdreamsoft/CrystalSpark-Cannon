<?php


/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 14:42
 */

use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html'; // Don't forget to configure your database in config.php


    echoTitle("Blockchain Events");

    $ethBlockchain = new EthereumBlockchain();
    $eventFactory = $ethBlockchain->getEventFactory();

    //we set a function to add blockchain event in our datagraph
    function createRandomTransaction(){

        $blockchain = new EthereumBlockchain();
        $ethereumAddressFactory = new EthereumAddressFactory();
        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        // we are counting the number of ethereum address just to be able to increment 0xDummySourceAddress1...2
        $count = $ethereumAddressFactory->countEntitiesOnRequest();

        //we create a dummy data, source and destination address contract, txid, block
        $dummySourceAddress = EthereumAddressFactory::getAddress("0xDummySourceAddress".$count,1);
        $dummyDestinationAddress = EthereumAddressFactory::getAddress("0xDummyDestinationAddress".$count,1);
        $dummyContract = $contractFactory::getContract("0xMyDummyContract",true, ERC721::init());
        $blockObject = \CsCannon\Blockchains\BlockchainBlockFactory::getOrCreateBlockWithId($count,$blockchain);
        $txId = "0xdummyTx".$count ;

        $ethBlockchain = new EthereumBlockchain();
        $eventFactory = $ethBlockchain->getEventFactory();
        $eventFactory->create($ethBlockchain,
            $dummySourceAddress,
            $dummyDestinationAddress,
            $dummyContract,$txId,time(),$blockObject);


    }
    createRandomTransaction();

    //load all events
    $blockchainEventFactory  = new \CsCannon\Blockchains\BlockchainEventFactory();

    $blockchainEventFactory->populateLocal(10,0);

    echoArray($blockchainEventFactory->getDisplay('array'));

















