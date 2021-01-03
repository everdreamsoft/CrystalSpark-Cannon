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
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Orb;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721 ;





final class MetadataSolverTest extends TestCase
{

    public const COLLECTION_NAME = "My First Collection2" ;
    public const COLLECTION_CODE = "MyFirstCollection2" ;
    public const COLLECTION_CONTRACT = "myContract2" ;

    public function testMetasolver()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);





        \CsCannon\Tests\TestManager::initTestDatagraph();

        $contractFactory = new EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());

        $pathSolver = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity("http://www.website.com/image/{tokenId}","http://www.website.com/meta/{tokenId}") ;
        $pathSolverOptional = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity("http://www.website.com/image/{tokenId}",
            "http://www.website.com/meta/{tokenId}",
            "http://www.fallback.com/meta/{tokenId}") ;
       // $pathSolver = LocalSolver::getEntity() ;

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());

        $collection = $assetCollectionFactory->create(self::COLLECTION_CODE,null, $pathSolver);
        $collectionWithOptional = $assetCollectionFactory->create(self::COLLECTION_CODE.'optional',null, $pathSolverOptional);
        $erc721 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
        $erc721->setTokenId($tokenId=2);



        $contract->bindToCollection($collection);
        $contract->bindToCollection($collectionWithOptional);



        /** @var \CsCannon\AssetCollection $collection */

        $collectionName = self::COLLECTION_NAME ;

        $collection->setName($collectionName);
        $collection->setImageUrl("https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png");
        $collection->setDescription("My collection is fantastic");
        $collection->setSolver($pathSolver);
        $collection->setSolver($pathSolverOptional);

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);

        $assets = $pathSolver::resolveAsset($collection,$erc721,$contract);
        $assetsWithOpt = $pathSolverOptional::resolveAsset($collection,$erc721,$contract);

        $this->assertInstanceOf(Asset::class,$assets[0]);

        $asset = reset($assets);
        /** @var Asset $asset */

        $this->assertEquals("http://www.website.com/image/$tokenId",$asset->imageUrl);









        //$addressEntity = $addressFactory->get($testAddress);
        //$balance = $addressEntity->getBalance();


     //   \CsCannon\Tests\TestManager::registerDataStructure();

    }

    public function testMetasolverReplace()
    {

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());

        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);
        $collection->setSolver(LocalSolver::getEntity());



        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);
        $solvers = $collection->getSolvers();

        //we should have two solvers one from the previous test and the local solver

        $this->assertCount(2,$solvers);

        //we remove one solver
        $bindings = $collection->getBrotherEntity(\CsCannon\AssetCollectionFactory::METADATASOLVER_VERB);
        $binding = end($bindings);
        $binding->delete();

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);
        $solvers = $collection->getSolvers();

        $this->assertCount(1,$solvers);

        //we rebind two solvers
        $collection->setSolver(LocalSolver::getEntity());

        $pathSolver = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity("http://www.website.com/image/{tokenId}","http://www.website.com/meta/{tokenId}") ;
        $collection->setSolver($pathSolver,1);

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);
        $solvers = $collection->getSolvers();

        $this->assertCount(1,$solvers);

        $contractFactory = new EthereumContractFactory();
        $erc721 = ERC721::init($tokenId=1);
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());
        $assets = $pathSolver::resolveAsset($collection,$erc721,$contract);

        $asset = reset($assets);
        /** @var Asset $asset */

        $this->assertEquals("http://www.website.com/image/$tokenId",$asset->imageUrl);










    }

    public function testSample()
    {





        $contractFactory = new EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());

        $pathSolver = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity("http://www.website.com/image/{{tokenId}}","http://www.website.com/meta/{{tokenId}}") ;
        // $pathSolver = LocalSolver::getEntity() ;

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);
        $erc721 =  ERC721::init(1);
        $erc721->setTokenId($tokenId=2);



        $asset[]  = $pathSolver::resolveAsset($collection, $erc721->setTokenId(2),$contract);
        $asset[]  = $pathSolver::resolveAsset($collection, $erc721->setTokenId(3),$contract);
        $asset[]  = $pathSolver::resolveAsset($collection, $erc721->setTokenId(4),$contract);

        $standards[] = ERC721::init(1);
        $standards[] = ERC721::init(2);
        $standards[] = ERC721::init(3);
        //$orb = new Orb($contract, $erc721->setTokenId(2),$collection,$asset);
       // $orbFactory = new \CsCannon\OrbFactory();
        //$orbs[] = $orbFactory->getOrbsFromContractPatKh($contract, $erc721->setTokenId(3));


        $collection->storeSample($standards);

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $assetCollectionFactory->populateLocal();
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);

        $standars = $collection->getStoredSamples();

        //Todo make tests
        $this->assertEquals(1,1);

       return $standards ;

        //$addressEntity = $addressFactory->get($testAddress);
        //$balance = $addressEntity->getBalance();


        //   \CsCannon\Tests\TestManager::registerDataStructure();

    }








}


