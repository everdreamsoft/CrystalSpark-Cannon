<?php

use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Counterparty\DataSource\XchainDataSource;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\DataSource\CrystalSuiteDataSource;
use CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI;
use CsCannon\Blockchains\Ethereum\DataSource\InfuraRopstenProvider;
use CsCannon\Blockchains\DataSource\RopstenDataSource;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\SandraManager;
use SandraCore\System;

require_once  '../../../autoload.php';


// $addressToQuery = '0xF68690FBa7319D98a2C580E7060e9fEFaF61eB27';
// $addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

// $address = $addressFactory->get($addressToQuery);

// $myAdressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

// $address = $myAdressFactory->get($addressToQuery);

// if ($myAdressFactory instanceof \CsCannon\Blockchains\Counterparty\XcpAddressFactory){

//     $address->setDataSource(new CrystalSuiteDataSource());
// }

// $balance = $address->getBalance();
// $tokens = $balance->getTokenBalance();

// echo $address->getAddress();

// print_r($balance->getTokenBalance());

// print_r(showSomething($tokens));

// function showSomething($tokens){

//     foreach($tokens as $token){

//         if(count($token['tokens']) > 1){
            
//             foreach($token['tokens'] as $value){

//                 foreach($value as $key => $truc){

//                     if($key == 'tokenId'){

//                         echo $key. '=>' .$truc.'<br/>';
//                     }
//                 }
//             }
//         }
//     }
// }

// $testAddress = BlockchainRouting::blockchainFromAddress($addressToQuery);

// print_r($testAddress);

// print_r($newTest);

//default = ether

// $sandra = new System($env = '', $install = false, $dbHost='127.0.0.1:3306', $db='sandra', $dbUsername='root', $dbpassword='root');

// SandraManager::setSandra($sandra);

// $sandra = new System();

// // $addressToQuery = '0x7dD743070471EdB18a2BeafEb62145cDeE6B3432';
// $addressToQuery = '1NDmzShZhN1SrFhWR9yvMYsA3pVLXW4Ffv';

// $addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

// $address = $addressFactory->get($addressFactory);
// $address->setDataSource( new CrystalSuiteDataSource);

// $address->setDataSource( new CrystalSuiteDataSource);

// if ($addressFactory instanceof XcpAddressFactory){
//     $address->setDataSource( new CrystalSuiteDataSource);
// }

// $balance = $address->getBalance();

// $balanceResponse = json_encode($balance->getTokenBalance());

// // Create new Asset

// $assetFactory = new AssetFactory();
// $asset = $assetFactory->create('myJoker', []);
// // print_r($asset);
// // var_dump($asset);
// $asset->setImageUrl('https://media.giphy.com/media/AwoDg0wJImOjK/source.gif');

// $ethContractFactory = new EthereumContractFactory();
// // $test = new EthereumAddressFactory()

// // if($ethContractFactory instanceof EthereumAddressFactory){
// //     echo 'ok';
// // }

// // print_r($ethContractFactory);
// // die;

// $jokerContract = $ethContractFactory->get('0x7dD743070471EdB18a2BeafEb62145cDeE6B3432', true);


// $assetCollectionFactory = new AssetCollectionFactory($sandra);

// // $mySolver = LocalSolver::getEntity();
// // $mySolver::getEntity();
// // print_r($assetCollectionFactory);
// // die;

// $myCollection = $assetCollectionFactory->getOrCreate('myFirstCollection');
// // N'arrive pas à appeler le LocalSolver
// $myCollection->setSolver(LocalSolver::getEntity());

// $jokerContract->bindToCollection($myCollection);
// $asset->bindToCollection($myCollection);
// $asset->bindToContract($jokerContract);

// // $assetSolver = LocalSolver::getEntity();


// // $balanceResponse = json_encode($balance->getObs());

// $balanceResponse = json_encode($balance->getObs());

// print_r($balanceResponse);

// $assetFactory = new \CsCannon\AssetFactory();
// $asset = $assetFactory->create('monDessin',[]);

// $asset->setImageUrl('https://i.pinimg.com/474x/b9/3f/b1/b93fb19e6313bdf565f73629b75c7c04.jpg');

// $xcpContractFactory = new \CsCannon\Blockchains\Counterparty\XcpContractFactory();
// $satoshiContract = $xcpContractFactory->get('SATOSHICARD',true);

// $assetCollectionFactory = new \CsCannon\AssetCollectionFactory($sandra);

// $myCollection = $assetCollectionFactory->getOrCreate("MyFirstBlockchainCollection");
// $myCollection->setSolver(\CsCannon\AssetSolvers\LocalSolver::getEntity());

// $satoshiContract->bindToCollection($myCollection);

// $asset->bindToCollection($myCollection);
// $asset->bindToContract($satoshiContract);

// $orbs = $balance->getObs();



// $sandra = new System();
// SandraManager::setSandra($sandra);

// $addressToQuery = '0xF68690FBa7319D98a2C580E7060e9fEFaF61eB27';
// $addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

// $address = $addressFactory->get($addressToQuery);
// $address->setDataSource( new RopstenDataSource('21AH14S1UUEXRJRPT6PA1HVCK5DVXHTT3V'));

// $assetFactory = new AssetFactory;
// $asset = $assetFactory->create('jokAsset', []);

// $asset->setImageUrl('https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg');

// $ethContractFactory = new EthereumContractFactory;

// $jokerContract = $ethContractFactory->get('0x634Ca1b8EB4C609C8ff7B616b9C7ca303Bfe65Be', true);

// $assetCollectionFactory = new AssetCollectionFactory($sandra);

// $myCollection = $assetCollectionFactory->getOrCreate('NewCollection');
// $myCollection->setSolver(LocalSolver::getEntity());

// $jokerContract->bindToCollection($myCollection);
// $asset->bindToCollection($myCollection);
// $asset->bindToContract($jokerContract);

// $balance = $address->getBalanceForContract([$jokerContract])->getTokenBalance();

// print_r($balance);

// TestNet
//jeans constantly especially wrap hero misery instead flirt edge define struggle first

$sandra = new System();
SandraManager::setSandra($sandra);

$addressToQuery = 'mzKVkdWvhfwoKyzyLg6wpxg9272etaqBC2';
$addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

$address = $addressFactory->get($addressToQuery);
$address->setDataSource( new XchainDataSource);

$assetFactory = new AssetFactory;
$asset = $assetFactory->create('A5948053354464580000', []);

$asset->setImageUrl('https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg');


$xcpContractFactory = new XcpContractFactory;

$jokerContract = $xcpContractFactory->get('A5948053354464580000', true);
$jokerContract->setDivisibility(1);

$assetCollectionFactory = new AssetCollectionFactory($sandra);

$myCollection = $assetCollectionFactory->getOrCreate('MyNewCollection');
$myCollection->setSolver(LocalSolver::getEntity());

$jokerContract->bindToCollection($myCollection);
$asset->bindToCollection($myCollection);
$asset->bindToContract($jokerContract);

$balance = $address->getBalance()->returnObsByCollections();

// var_dump(round(showNumber($address)));

$nombre = round($address->getBalance()->getTokenBalance()[0]['tokens'][0]['quantity']);
$number = intval($nombre);

$image = $asset->imageUrl;
var_dump($image);
// var_dump($asset->getImageUrl());

function findSource($balance){

    foreach($balance as $newBalance){
        foreach($newBalance as $findOrbs){
            foreach($findOrbs as $newTruc){
                if(is_array($newTruc)){
                    foreach($newTruc as $otherTruc){
                        // var_dump($newKey['asset']['image']);
                        $imageToDisplay = $otherTruc['asset']['image'];
                    }
                }
            }
        }
    }
    return $imageToDisplay;
}

// var_dump(findSource($balance));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <?php for($i=0; $i<$number; $i++) { ?>
        <img src="<?php echo $image ?>">
    <?php } ?>

    <!-- <img src ='https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg' > -->

</body>
</html>