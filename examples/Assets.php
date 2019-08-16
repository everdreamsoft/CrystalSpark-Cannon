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

$system = new System('shab');
SandraManager::setSandra($system);



$assetFactory = new AssetFactory();
$assetFactory->populateLocal();

print_r($assetFactory->dumpMeta());



