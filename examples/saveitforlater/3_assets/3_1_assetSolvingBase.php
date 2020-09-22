<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.09.20
 * Time: 18:23
 */

use CsCannon\AssetCollectionFactory;
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
     seven ERC-721 tokens within four different contracts. But for this example let's focus on Blockchain Cutties contract
    0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe
    ");


    $myTestEthereumAddress = EthereumAddressFactory::getAddress($testEthAddress,true); //get an address object from the factory
    $contract = EthereumContractFactory::getContract('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe',true);

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaDataSource());
    $balance = $myTestEthereumAddress->getBalanceForContract(array($contract)); // this time we do get balance for a single contract
    //note we are passing an array of contracts as parameters. Opensea datasource has at the time of writing issue with querying multiple contracts.



    echoExplanations("The token balance for this specific contract is as follow :
    ");

    echoArray($balance->getTokenBalanceArray());

    echoExplanations("If the balance didn't change we should have 2 ERC721 tokens with tokenIds 47225 30450
    ");

    echoExplanations("In order to get asset out of these token we need to define an asset solver attached to a collection and a contract
    ");

    $assetCollectionFactory = new AssetCollectionFactory(\CsCannon\SandraManager::getSandra());


    echoSubTitle("Build the collection");

    //is the collection already available in our datagraph ?
    if(!$assetCollectionFactory->get("blockchaincuties")){
        //collection doesn't exist so we create it

        /* we set an array of data relative to our collection
        You can pass any data in the form of Key => Value array.
        keep the key short using CamelCase. For the value keep a string of less than 255 characters (Varchar 255)
        these are called references
        */
        $collectionData = array("name"=>'Blockchain Cuties',
            "description","Collection added during tutorial",
            "myCustomValue" => "custom",
            "camelCaseForKey" => "string of 255 characters",
            );

        $bcCollection = $assetCollectionFactory->create('blockchaincuties',$collectionData);

        //there are a set of premade functions to define collection like
        $bcCollection->setImageUrl('https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcTnLkShESwS5l1NbbhiGB44o8fD6lGfK0ud0w&usqp=CAU'); // the logo
        $bcCollection->setDescription("Collection added during tutorial then modified"); // this will ovveride the description set above

        //you can also any data as a form of key value using sandra command craeteOrUpdateRef
        $bcCollection->createOrUpdateRef("aNewCustomField","no field is too much");




        //now we have to put blockchaincuties contract in the collection

        $contract->bindToCollection($bcCollection);
        echoExplanations("we bound the contract ".$contract->getId()." to the collection".$bcCollection->get("name"));

    }

    echoCode('
    $assetCollectionFactory = new AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
    
    $collectionData = array("name"=>\'Blockchain Cuties\',
            "description","Collection added during tutorial",
            "myCustomValue" => "custom",
            "camelCaseForKey" => "string of 255 characters",
            );
            
      $bcCollection = $assetCollectionFactory->create(\'blockchaincuties\',$collectionData);
            
            '
    );

    echoSubTitle("Display the collection");

    $collectionTable = '';
    $assetCollectionFactory = new AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
    $assetCollectionFactory->populateLocal();
    foreach ($assetCollectionFactory->getEntities() as $collection){

        $referencesToDisplay = ['name','description','myCustomValue'];
        $line = '';

        foreach ($referencesToDisplay as $referenceName)  {
            $line .=  buildTd($collection->get($referenceName));
        }

        $collectionTable .= buildTr($line);

    }

    echoHTMLTable($collectionTable,$referencesToDisplay);






