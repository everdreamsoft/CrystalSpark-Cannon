<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 31/07/2019
 * Time: 20:17
 */


namespace CsCannon ;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload



use CsCannon ;
use SandraCore\System;

$system = new System('alpha');
SandraManager::setSandra($system);


$assetCollection = new AssetCollectionFactory(SandraManager::getSandra());

$contractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory();
$contractFactory->populateLocal();


$assetCollection->populateLocal();

print_r($contractFactory->dumpMeta());




