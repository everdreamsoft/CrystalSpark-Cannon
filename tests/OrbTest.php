<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Asset;
use CsCannon\AssetSolvers\BooSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Orb;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;





final class OrbTest extends TestCase
{

    public const COLLECTION_NAME = "My First Collection" ;
    public const COLLECTION_CODE = "MyFirstCollection" ;
    public const COLLECTION_CONTRACT = "myContract" ;

    public function testCollection()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $assetCollectionFactory->create(self::COLLECTION_CODE,null, \CsCannon\AssetSolvers\LocalSolver::getEntity());

        /** @var \CsCannon\AssetCollection $collection */

        $collectionName = self::COLLECTION_NAME ;

        $collection->setName($collectionName);
        $collection->setImageUrl("https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png");
        $collection->setDescription("My collection is fantastic");
        $collection->setSolver(LocalSolver::getEntity());

        $this->assertInstanceOf(\CsCannon\AssetCollection::class,$collection,"Collection Factory
        Does not return valid collection object");

        $this->assertEquals($collectionName,$collection->name,"Collection name not pass");

        $assetCollectionControl = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionControl->populateLocal();
        $collection = reset($assetCollectionControl->entityArray) ;


        $this->assertCount(1,$assetCollectionControl->entityArray,"Collection not saved");
        $this->assertEquals($collectionName,$collection->name,"Collection name not pass");

        //$addressEntity = $addressFactory->get($testAddress);
        //$balance = $addressEntity->getBalance();


     //   \CsCannon\Tests\TestManager::registerDataStructure();

    }

    public function testAsset()

    {

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
       $collectionEntity = $assetCollectionFactory->get(self::COLLECTION_CODE);

        //create a contract
        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true);
        $contract->bindToCollection($collectionEntity);


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

        foreach (($getJoinedCollection ? $getJoinedCollection : array()) as $collection)
            $this->assertInstanceOf(\CsCannon\AssetCollection::class,$collection,"Asset collection is not a collection");
       // $this->assertInstanceOf(\CsCannon\Asset::class,$asset,"Asset factory didn't produce an asset");

       // $getJoinedContract->dumpMeta();

        $myOrbFactory = new \CsCannon\OrbFactory();
        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,ERC20::getEntity());

        $this->assertInstanceOf(Orb::class,$orbs[0],"Orb couldn't be retreived");

        //we look if the local asset solver works
        $orb = reset($orbs);
        /**@var Orb $orb*/
        $orbAsset = $orb->getAsset();

        $this->assertInstanceOf(Asset::class,$orbAsset,"Orbs asset is not an asset");

        \CsCannon\Tests\TestManager::registerDataStructure();


    }

    public function testOrb()

    {


        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        //$collectionEntity = $assetCollectionFactory->get(self::COLLECTION_CODE);
        //BooSolver::update();

        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT);

        $myOrbFactory = new \CsCannon\OrbFactory();
       $orbs = $myOrbFactory->getOrbsFromContractPath($contract,CounterpartyAsset::getEntity());





        $this->assertEquals(1,1);
        //$this->assertInstanceOf(\CsCannon\Asset::class,$asset,"Asset contract is not a contract");

       // \CsCannon\Tests\TestManager::registerDataStructure();



    }






}

class myEasyBooSolver extends BooSolver {

    public static $hardLimit = 5 ;
    public static $limitToTokens = array(TestManager::XCP_TOKEN_AVAIL);
    public static $limitCollection = array(TestManager::LIMIT_TO_COLLECTIONS);



}
