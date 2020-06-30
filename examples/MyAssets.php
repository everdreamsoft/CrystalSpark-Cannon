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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$sandra = new \SandraCore\System('discover',true);
\CsCannon\SandraManager::setSandra($sandra);




$assetFactory = new \CsCannon\AssetFactory();
$assetFactory->populateLocal();

print_r($assetFactory->getDisplay('array'));



//$assetCollectionFactory = new CsCannon\AssetCollectionFactory(CsCannon\SandraManager::getSandra());
//print_r($assetCollectionFactory->getDisplay('a'));



