<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.09.20
 * Time: 18:23
 */

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetSolvers\LocalSolver;
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



    echoTitle("Asset Solving : Local Solver");
    echoTitle("What is local solving");
    echoExplanations("On our previous tutorial we learned how to use path predictable solver. The path predictable 
    solver assumes there is external resources to get a an asset image related to a token. 
    This solver doesn't store anything in your datagraph
    this means you cannot index or make queries to search particular assets.
    ");

    echoExplanations("This time let's take a look at an important way of solving asset : The local solver. The local solver
    allows to keep local information about assets. This is particulary useful if we want to make asset searchable and make queries on them.
    This is also useful if you want to create your own tokenized asset
    ");

    echoTitle("Use case");

    //first off let's create our own collection
    $assetFactoryCollection = new AssetCollectionFactory();
    /* this time we pas the local solver directly on the collection creation instead of
    making $myNewCollection->setSolver(LocalSolver::getEntity())
    */
    $myNewCollection = $assetFactoryCollection->getOrCreate('MyFirstCollection', LocalSolver::getEntity());
    $myNewCollection->setDescription("the first collection made in tutorial 3_2");

    echoExplanations("This time we want our collection t
    ");



    $assetFactory = new \CsCannon\AssetFactory();









require_once '../viewFooter.html';




