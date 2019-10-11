<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-26
 * Time: 15:48
 */


namespace CsCannon ;

use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload

const COLLECTION_CODE = "Yummy" ;

//Example token
const EXAMPLE_ERC20_CONTRACT = "0x89d24a6b4ccb1b6faa2625fe562bdd9a23260359" ; //DAI contract


$assetCollectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
$collectionEntity = $assetCollectionFactory->getOrCreate(COLLECTION_CODE);
$assetCollectionFactory->populateLocal();


// Asset
$contractFactory = new EthereumContractFactory();
$contract = $contractFactory->get(EXAMPLE_ERC20_CONTRACT,true);


$assetFactory = new AssetFactory(SandraManager::getSandra());
$metaData = [AssetFactory::IMAGE_URL=>'http://www.google.com',
    AssetFactory::METADATA_URL =>"http://www.google.com"
];


$asset = $assetFactory->create('hello',$metaData, [$collectionEntity],[$contract]);

echo"end";


