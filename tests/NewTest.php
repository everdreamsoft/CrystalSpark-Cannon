<?php

use CsCannon\Asset;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
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

require_once  '../../../autoload.php';


/**
 * createNewAsset with existing sandra address, contract, image and dataSource
 * @param System $sandra
 * @param String $address
 * @param String $contract
 * @param BlockchainDataSource $datasource
 * @param String $url
 * @param String|null $collectionName
 * @param Array|null $metaData
 * @return void
 */
function createNewAsset(
    System $sandra,
    Blockchain $blockhain,
    string $contract,
    string $url,
    string $collectionName,
    array $metaData = []): Asset
    {


    SandraManager::setSandra($sandra);

    $assetFactory = new AssetFactory;
    $asset = $assetFactory->create($contract, $metaData);
    $asset->setImageUrl($url);

    // $newContractFactory = findContractFactory($contract);
    $newContractFactory = $blockhain->getContractFactory();
    $newContract = $newContractFactory->get($contract, true);

    $assetCollectionFactory = new AssetCollectionFactory($sandra);
        
    $myCollection = $assetCollectionFactory->getOrCreate($collectionName);
    $myCollection->setSolver(LocalSolver::getEntity());

    $newContract->bindToCollection($myCollection);
    $asset->bindToCollection($myCollection);
    $asset->bindToContract($newContract);

    return $asset;

}

function findContractFactory(string $contract): BlockchainContractFactory
{

    $unknownFactory = get_class(BlockchainRouting::getAddressFactory($contract));

    switch($unknownFactory){

        case 'CsCannon\Blockchains\Counterparty\XcpAddressFactory':
            return new XcpContractFactory;
        break;

        case 'CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticAddressFactory':
            return new MaticContractFactory;
        break;

        case 'CsCannon\Blockchains\Klaytn\KlaytnAddressFactory':
            return new KlaytnContractFactory;
        break;

        case 'CsCannon\Blockchains\FirstOasis\FirstOasisAddressFactory':
            return new FirstOasisContractFactory;
        break;

        case 'CsCannon\Blockchains\Ethereum\EthereumAddressFactory':
            return new EthereumContractFactory;
        break;

    }

}

$sandra = new System();

$myAsset = createNewAsset(
    $sandra, 
    new EthereumBlockchain,
    'A5948053354464580000',
    'https://static.fnac-static.com/multimedia/Images/FD/Comete/123455/CCP_IMG_ORIGINAL/1608833.jpg',
    'MyNewCollection'
);


$addressToQuery = "mzKVkdWvhfwoKyzyLg6wpxg9272etaqBC2";
$addressFactory = BlockchainRouting::getAddressFactory($addressToQuery);

$address = $addressFactory->get($addressToQuery);
$address->setDataSource(new XchainDataSource);

$balance = $address->getBalance()->returnObsByCollections();

print_r($balance);


$image = $myAsset->getImageUrl();
// $myAsset->get

$nombre = floor($address->getBalance()->getTokenBalance()[0]['tokens'][0]['quantity']);
$number = intval($nombre);

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