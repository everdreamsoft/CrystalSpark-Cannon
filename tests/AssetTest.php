<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\SandraManager;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;

final class AssetTest extends TestCase
{
    public function testGetContracts(){

        TestManager::initTestDatagraph();
        $sandra = SandraManager::getSandra();

        $myCollection = new AssetCollectionFactory($sandra);
        $newCollection = $myCollection->getOrCreate('testCollection');
        $newCollection->setName('test');

        $assetFactory = new AssetFactory;
        $asset = $assetFactory->create('assetTest', []);

        $asset->bindToCollection($newCollection);


        $contractFactory = new XcpContractFactory;
        $contract = $contractFactory->get('A1417599316207124900', true);
    

        $contract->bindToCollection($newCollection);

        $asset->bindToContract($contract);


        $this->assertNotNull($asset->getContracts(), $message ='fail');


    }
}