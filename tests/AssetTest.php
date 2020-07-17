<?php

use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\SandraManager;
use PHPUnit\Framework\TestCase;
use SandraCore\System;

class AssetTest extends TestCase
{
    public function testAsset(){

        $sandra = new System('', true, '127.0.0.1:3306', 'sandra', 'root', '');
        SandraManager::setSandra($sandra);

        $myCollection = new AssetCollectionFactory($sandra);
        $newCollection = $myCollection->getOrCreate('testCollection');
        $newCollection->setName('test');

        $assetFactory = new AssetFactory;
        $asset = $assetFactory->create('assetTest', []);

        $asset->bindToCollection($newCollection);


        $contractFactory = new XcpContractFactory;
        $contract = $contractFactory->get('A1417599316207124900', true);

        $contract->bindToCollection($newCollection);

        $contract->bindToAsset($asset);


        $assetContract = $asset->getContracts();

        return $assetContract;

    }
}