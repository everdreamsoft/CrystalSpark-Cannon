<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 31/07/2019
 * Time: 20:17
 */

//ethereum address

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon ;

$defaultXcpAddress = '186nXV8gY3LC1fjoTDGcieJqhk7ETgmPNM';

$xcpAddress = $defaultXcpAddress;


$addressFactory = CsCannon\BlockchainRouting::getAddressFactory($xcpAddress);

$addressEntity = $addressFactory->get($xcpAddress);

$balance = $addressEntity->getBalance();

print_r($balance);


$defaultEthereumAddress = '0x7f7eed1fcbb2c2cf64d055eed1ee051dd649c8e7';

$ethereumAddress = $defaultEthereumAddress;


$addressFactory = CsCannon\BlockchainRouting::getAddressFactory($ethereumAddress);

$addressEntity = $addressFactory->get($ethereumAddress);

$balance = $addressEntity->getBalance();

print_r($balance);

$assetCollectionFactory = new CsCannon\AssetCollectionFactory(CsCannon\SandraManager::getSandra());
print_r($assetCollectionFactory->getDisplay('a'));



