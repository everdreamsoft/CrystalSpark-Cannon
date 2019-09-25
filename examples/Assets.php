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

//$system = new System('alpha');
SandraManager::getSandra();



$assetFactory = new AssetFactory(SandraManager::getSandra());

$assetFactory->populateLocal();

$xcpContractFactory = new CsCannon\Blockchains\Counterparty\XcpContractFactory();
//$xcpContractFactory->get





