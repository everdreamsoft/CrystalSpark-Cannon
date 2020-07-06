<?php

use CsCannon\Asset;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Counterparty\DataSource\XchainDataSource;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticContractFactory;
use CsCannon\Blockchains\FirstOasis\FirstOasisContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnContractFactory;
use CsCannon\SandraManager;
use SandraCore\System;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * createNewAsset with existing sandra address, contract, image and dataSource
 * @param System $sandra
 * @param BlockchainContract $contract
 * @param Asset $asset
 * @param String $url
 * @return void
 */
function createNewAsset(
    System $sandra,
    BlockchainContract $contract,
    Asset $asset,
    string $url): Asset
    {

    SandraManager::setSandra($sandra);

    $asset->setImageUrl($url);
    $collectionName = $asset->id;

    $assetCollectionFactory = new AssetCollectionFactory($sandra);
        
    $myCollection = $assetCollectionFactory->getOrCreate($collectionName);
    $myCollection->setSolver(LocalSolver::getEntity());

    $contract->bindToCollection($myCollection);
    $asset->bindToCollection($myCollection);
    $asset->bindToContract($contract);

    return $asset;

}


// Create New Asset

$sandra = new System();

$xcpContract = new XcpContractFactory;
$myContract = $xcpContract->get("A5948053354464580000");

// $assetCollectionFactory = new AssetCollectionFactory($sandra);
// $assetCollectionFactory->getOrCreate("OneMore");

$assetFactory = new AssetFactory;
$myCollection = $assetFactory->create("MyCollection", []);
// $assetFactory->joinCollection($assetCollectionFactory);

$myAsset = createNewAsset(
    $sandra, 
    $myContract,
    $myCollection,
    'https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg'
);



// Get Balance

$addressToQuery = "mzKVkdWvhfwoKyzyLg6wpxg9272etaqBC2";
$addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

$address = $addressFactory->get($addressToQuery);
$address->setDataSource(new XchainDataSource("testnet"));
$assetStandard = $myContract->getStandard();

$balance = $address->getBalance()->returnObsByCollections();

$number = $address->getBalance()->getBalanceForToken($myContract, $assetStandard);

print_r($balance);

$image = $myAsset->getImageUrl();


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

</body>
</html>