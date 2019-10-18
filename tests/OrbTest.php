<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\AssetSolvers\LocalSolver;
use PHPUnit\Framework\TestCase;





final class OrbTest extends TestCase
{

    public const COLLECTION_NAME = "My First Collection" ;
    public const COLLECTION_CODE = "MyFirstCollection" ;
    public const COLLECTION_CONTRACT = "myContract" ;

    public function testCollection()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();


        $testAddress = \CsCannon\Tests\TestManager::XCP_TEST_ADDRESS;

        $addressFactory = CsCannon\BlockchainRouting::getAddressFactory($testAddress);

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $assetCollectionFactory->create(self::COLLECTION_CODE,null, \CsCannon\AssetSolvers\BooSolver::getEntity());

        /** @var \CsCannon\AssetCollection $collection */

        $collectionName = self::COLLECTION_NAME ;

        $collection->setName($collectionName);
        $collection->setImageUrl("https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png");
        $collection->setDescription("My collection is fantastic");

        $this->assertInstanceOf(\CsCannon\AssetCollection::class,$collection,"Collection Factory
        Does not return valid collection object");

        $this->assertEquals($collectionName,$collection->name,"Collection name not pass");




        $assetCollectionControl = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionControl->populateLocal();
        $collection = reset($assetCollectionControl->entityArray) ;


        $this->assertCount(1,$assetCollectionControl->entityArray,"Collection not saved");
        $this->assertEquals($collectionName,$collection->name,"Collection name not pass");




        //$assetCollectionFactory->create('myFirstCOllection',null);

        //First we create an Asset

        $assetFactory = new \CsCannon\AssetFactory(\CsCannon\SandraManager::getSandra());



        $addressEntity = $addressFactory->get($testAddress);
        $balance = $addressEntity->getBalance();
        $balance->getObs();

        \CsCannon\Tests\TestManager::registerDataStructure();






    }

    public function testAsset()

    {

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
       $collectionEntity = $assetCollectionFactory->get(self::COLLECTION_CODE);

        //create a contract
        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true);


       $assetFactory = new \CsCannon\AssetFactory(\CsCannon\SandraManager::getSandra());
       $metaData = [\CsCannon\AssetFactory::IMAGE_URL=>'http://www.google.com',
           \CsCannon\AssetFactory::METADATA_URL =>"http://www.google.com"
           ];


       $asset = $assetFactory->create('hello',$metaData, [$collectionEntity],[$contract]);

        $this->assertInstanceOf(\CsCannon\Asset::class,$asset,"Asset factory didn't produce an asset");



       $getJoinedContract = $asset->getContracts();
       $getJoinedCollection= $asset->getCollections();

        foreach (($getJoinedContract ? $getJoinedContract : array()) as $contract)
        $this->assertInstanceOf(\CsCannon\Blockchains\BlockchainContract::class,$contract,"Asset contract is not a contract");

        foreach (($getJoinedCollection ? $getJoinedCollection : array()) as $contract)
            $this->assertInstanceOf(\CsCannon\AssetCollection::class,$contract,"Asset collection is not a collection");
       // $this->assertInstanceOf(\CsCannon\Asset::class,$asset,"Asset factory didn't produce an asset");

       // $getJoinedContract->dumpMeta();

        \CsCannon\Tests\TestManager::registerDataStructure();





    }

    public function testOrb()

    {


        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collectionEntity = $assetCollectionFactory->get(self::COLLECTION_CODE);
        \CsCannon\AssetSolvers\BooSolver::update();

        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT);

        $myOrbFactory = new \CsCannon\OrbFactory();
        $myOrbFactory->getOrbsFromContractPath($contract,new \CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset());



        $this->assertEquals(1,1);
        //$this->assertInstanceOf(\CsCannon\Asset::class,$asset,"Asset contract is not a contract");

        \CsCannon\Tests\TestManager::registerDataStructure();



    }







}
