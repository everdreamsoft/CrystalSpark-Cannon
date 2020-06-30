<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 24.06.20
 * Time: 10:29
 */

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\SandraManager;
use SandraCore\System;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload


$sandra = new System();
SandraManager::setSandra($sandra);
$addressToQuery = '0x7dD743070471EdB18a2BeafEb62145cDeE6B3432';
// $addressToQuery = '1DMqFAxwT85LxLQDKe5EoPxBYcUfEQtbbZ';
$addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);
$address = $addressFactory->get($addressFactory);
// $address->setDataSource( new CrystalSuiteDataSource);
// if ($addressFactory instanceof XcpAddressFactory){
//     $address->setDataSource( new CrystalSuiteDataSource);
// }
$balance = $address->getBalance();
// $balanceResponse = json_encode($balance->getTokenBalance());
// Create new Asset
// $assetFactory = new AssetFactory();
// $asset = $assetFactory->create('myJoker', []);
$assetFactory = new CsCannon\AssetFactory;
$asset = $assetFactory->create('jokAsset', []);
// print_r($asset);
// var_dump($asset);
$asset->setImageUrl('https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg');
$ethContractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory;
// $test = new EthereumAddressFactory()
$jokerContract = $ethContractFactory->get('jokCard', true);
$assetCollectionFactory = new CsCannon\AssetCollectionFactory($sandra);
$assetCollectionFactory->populateLocal();
// $mySolver = LocalSolver::getEntity();
// $mySolver::getEntity();
// print_r($assetCollectionFactory);
// die;
$myCollection = $assetCollectionFactory->getOrCreate('NewCollection', null);
// $myCollection->setSolver(LocalSolver::getEntity());
$jokerContract->bindToCollection($myCollection);
$asset->bindToCollection($myCollection);
$asset->bindToContract($jokerContract);
// $assetSolver = LocalSolver::getEntity();
// $balanceResponse = json_encode($balance->getObs());
$balanceResponse = json_encode($balance->getObs());
print_r($balanceResponse);

die("asdfdsaf");


$sandra = new \SandraCore\System('discover',true);
\CsCannon\SandraManager::setSandra($sandra);



$addressToQuery = '1mzm8NqodUuuxip3uSoDrXraCXkmmwDcq';
//$addressToQuery = '0x7f7EED1fcBb2C2cf64d055eED1Ee051DD649C8e7';

$addressFactory = \CsCannon\BlockchainRouting::getAddressFactory($addressToQuery);

$address = $addressFactory->get($addressToQuery);



if ($addressFactory instanceof \CsCannon\Blockchains\Counterparty\XcpAddressFactory){
    $address->setDataSource(new \CsCannon\Blockchains\DataSource\CrystalSuiteDataSource());
    //TODO fix datasource default for xcp not working
}

$balance = $address->getBalance();

$balanceResponse = json_encode($balance->getTokenBalance());

// create asset

$assetFactory = new \CsCannon\AssetFactory();
$asset = $assetFactory->create('monDessin',[]);

$asset->setImageUrl('https://i.pinimg.com/474x/b9/3f/b1/b93fb19e6313bdf565f73629b75c7c04.jpg');

$xcpContractFactory = new \CsCannon\Blockchains\Counterparty\XcpContractFactory();
$satoshiContract = $xcpContractFactory->get('SATOSHICARD',true);

$assetCollectionFactory = new \CsCannon\AssetCollectionFactory($sandra);
$myCollection = $assetCollectionFactory->getOrCreate("MyFirstBlockchainCollection");
$myCollection->setSolver(\CsCannon\AssetSolvers\LocalSolver::getEntity());


$satoshiContract->bindToCollection($myCollection);
$asset->bindToCollection($myCollection);
$asset->bindToContract($satoshiContract);

$orbs = $balance->getObs();



    // https://i.pinimg.com/474x/b9/3f/b1/b93fb19e6313bdf565f73629b75c7c04.jpg

$balanceResponse = json_encode($balance->returnObsByCollections());

echo $balanceResponse;




