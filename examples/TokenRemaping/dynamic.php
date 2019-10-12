<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-26
 * Time: 15:48
 */


namespace CsCannon ;

use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload

const COLLECTION_CODE = "Yummy" ;

//Example token
const EXAMPLE_ERC20_CONTRACT = "0x89d24a6b4ccb1b6faa2625fe562bdd9a23260359" ; //DAI contract
const EXAMPLE_HOLDER_ADDRESS = "0x1a84d1c0258bdc26f013218acb2530a76c884a38" ; //DAI contract


$assetCollectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
$collectionEntity = $assetCollectionFactory->getOrCreate(COLLECTION_CODE);
$assetCollectionFactory->populateLocal();


// Asset
$contractFactory = new EthereumContractFactory();
$contract = $contractFactory->get(EXAMPLE_ERC20_CONTRACT,true);
$contract->bindToCollection($collectionEntity); // should be this trivial ?




$assetFactory = new AssetFactory(SandraManager::getSandra());
$metaData = [AssetFactory::IMAGE_URL=>'http://www.google.com',
    AssetFactory::METADATA_URL =>"http://www.google.com"
];


$asset = $assetFactory->create('hello',$metaData, [$collectionEntity],[$contract]);

//query the balance
$etherAddressFactory = new EthereumAddressFactory();
$balance = $etherAddressFactory->get('0x1a84d1c0258bdc26f013218acb2530a76c884a38')
    ->setDataSource(new BlockscoutAPI())
    ->getBalance()
    ->returnObsByCollections();


print_r($balance);






echo"end";


