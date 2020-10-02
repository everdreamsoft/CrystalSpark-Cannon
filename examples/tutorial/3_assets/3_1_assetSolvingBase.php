<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.09.20
 * Time: 18:23
 */

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetSolvers\PathPredictableSolver;
use CsCannon\Balance;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



//require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html';



    echoSubTitle("Asset Solving");
    echoExplanations("On our tutorial 1_1_tokenBalance we learned how to get token balance from an address
    but this is not very sexy. In this tutorial we are going to look how we can retreive assets from a token contract
    ");


    echoExplanations("the balance of our example address 0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79 shows
     seven ERC-721 tokens within four different contracts. But for this example let's focus on Blockchain Cuties contract
    0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe
    ");

    $testEthAddress = '0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79';

    $myTestEthereumAddress = EthereumAddressFactory::getAddress($testEthAddress,true); //get an address object from the factory
    $contract = EthereumContractFactory::getContract('0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe',true);

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaDataSource());
    $balance = $myTestEthereumAddress->getBalanceForContract(array($contract)); // this time we do get balance for a single contract
    //note we are passing an array of contracts as parameters. Opensea datasource has at the time of writing issue with querying multiple contracts.

    echoCode(' $testEthAddress = \'0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79\';
    
    $myTestEthereumAddress = EthereumAddressFactory::getAddress($testEthAddress,true); //get an address object from the factory
    $contract = EthereumContractFactory::getContract(\'0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe\',true);

    $myTestEthereumAddress->setDataSource(new \CsCannon\Blockchains\Ethereum\DataSource\OpenSeaDataSource());
    $balance = $myTestEthereumAddress->getBalanceForContract(array($contract)); // this time we do get balance for a single contract
    //note we are passing an array of contracts as parameters. Opensea datasource has at the time of writing issue with querying multiple contracts.');



    echoExplanations("The token balance for this specific contract is as follow :
    ");

    echoCode('$balance->getTokenBalanceArray()');
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


        /*now we are going to tag this collection in order to filter collection created during this tutorial and not
        other collection available in our current datagraph
        for this we create a new relation collection->belontsTo->3_1_assetSolvingBase we will filter on that later on
        */
        $bcCollection->setBrotherEntity('belongsTo','3_1_assetSolvingBase',[]);

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

    //here we are making sure we are taking the one collection we created here
    $assetCollectionFactory->setFilter('belongsTo','3_1_assetSolvingBase');

    $assetCollectionFactory->populateLocal();
    foreach ($assetCollectionFactory->getEntities() as $collection){

        $referencesToDisplay = ['name','description','myCustomValue','aNewCustomField'];
        $line = '';

        foreach ($referencesToDisplay as $referenceName)  {
            $line .=  buildTd($collection->get($referenceName));
        }

        $collectionTable .= buildTr($line);

    }

    echoHTMLTable($collectionTable,$referencesToDisplay);

    echoSubTitle("Asset Solving");

    echoExplanations("There are multiple ways to solve asset. Meaning converting a token balance to an asset, in our
    case displaying the cuties image from the collection. The simplest way is to use the path predictable solver.
    Path predicatble convert a token reference, in our case tokenId, into an image URL.
    
    In the case of blockchain cuties resource image are located on blockchain cuties website in the form of
    https://blockchaincuties.com/rest/svgap/3/{tokenId}.svg {tokenId} to be replaced by the ERC-721 tokenId.
    <br> For example 
    <a href='https://blockchaincuties.com/rest/svgap/3/47225.svg'>
    https://blockchaincuties.com/rest/svgap/3/47225.svg</a>
    <br> Path predictable solver always take the image URI as first parameter, the JSON metadata URI path and optionnaly
    a fallback image URI if the image is not found. You can pass any token data under curly bracket {anyTokenData} 
    the data will be replaced by the actual token data. On this particual case as we are using ERC-721 standard we are
    using {tokenId}
    
    
    ");
    echoExplanations("we are binding the solver to the collection");
    echoCode('$pathSolver = PathPredictableSolver::getEntity("https://blockchaincuties.com/rest/svgap/3/{tokenId}.svg","https://blockchaincuties.com/rest/svgap/3/{tokenId}.svg") ;');

    /* note this time we are not creating a solver from a factory but we are using getEntity. The reason is we are using
    and existing solver entity existing in CSCannon. So when handling solvers always use SolverName::getEntity()

    */
    $pathSolver = PathPredictableSolver::getEntity("https://blockchaincuties.com/rest/svgap/3/{tokenId}.svg","https://blockchaincuties.com/rest/svgap/3/{tokenId}.svg") ;

    echoCode('$bcCollection->setSolver($pathSolver);');

    //did we just create the collection and the solver ?
    if (isset($bcCollection)) {
        // we do this step only if we just created the collection. if $bcCollection exist in means it has been created above
        $bcCollection->setSolver($pathSolver);

        echoExplanations("We just added the collection in our datagraph in order for CSCannon to be able to resolve
        our assets please reload the page as our collection and solver data are fresh we need to have updated data
        
        ");
        //So we stop the process right here
        die();


    }

    echoExplanations("Now our CScannon is able to resolve assets and collection out of our balance");

    echoCode('$balance->returnObsByCollections()');
    echoArray($balance->returnObsByCollections());

    echoExplanations("Now let's display our balance in a more visual way");

    $bcCollection = $assetCollectionFactory->get('blockchaincuties');

    //let's create a function to display balances that we can reuse in
    function displayBalancePerCollection(Balance $balance, AssetCollection $collection)
    {

        //we display collection header
        echo"<div class=\"card\">
      <h5 class=\"card-header\">Balance for ".$balance->address->getAddress()."</h5>
      <div class=\"card-body\">
        <h5 class=\"card-title\">".$collection->name."</h5>
        <p class=\"card-text\">".$collection->description."</p>
       
      </div>
    </div>";

        $orbFactory = $balance->getObs();
        echo '<div class="row">';
        foreach ($orbFactory->getOrbsInCollection($collection) as $orb) {

            echo "<div class=\"card\" style=\"width: 18rem;\">
              <img class=\"card-img-top\" src=\"" . $orb->getAsset()->imageUrl . "\" alt=\"Card image cap\">
              <div class=\"card-body\">
                <h5 class=\"card-title\">".$orb->tokenSpecifier->getDisplayStructure()."</h5>
                <p class=\"card-text\">Quantity :".$orbFactory->getOrbQuantity($orb)."</p>
               
              </div>
            </div>";

        }
        echo '</div>';
    }

    displayBalancePerCollection($balance,$bcCollection);




require_once '../viewFooter.html';




