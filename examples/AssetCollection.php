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

$system = new System('',true);
SandraManager::setSandra($system);


$assetCollection = new AssetCollectionFactory(SandraManager::getSandra());

//$contractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory()
//$contractFactory->populateLocal();


$assetCollection->populateLocal();
//print_r($assetCollection->dumpMeta());
//die();
//$assetCollection->create

// I want to get the first cryptokitties

$ck = $assetCollection->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');
$specifier = new CsCannon\Blockchains\Ethereum\Interfaces\ERC721();
$specifier->setToken(array('tokenId2'=>1));

$contractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory();
$contract = $contractFactory->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');

$orbFactory = new OrbFactory();
$orb = OrbFactory::getOrbFromSpecifier($specifier,$contract,$ck);
$orb->getAsset();

echo"finish ".print_r($orb->getAsset());







