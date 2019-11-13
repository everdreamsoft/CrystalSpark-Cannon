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





final class MetadataSolverTest extends TestCase
{

    public const COLLECTION_NAME = "My First Collection" ;
    public const COLLECTION_CODE = "MyFirstCollection" ;
    public const COLLECTION_CONTRACT = "myContract" ;

    public function testCollection()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);


        \CsCannon\Tests\TestManager::initTestDatagraph();

        $contractFactory = new EthereumContractFactory();
        $contract = $contractFactory->get(self::COLLECTION_CONTRACT,true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());

        $pathSolver = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity("http://www.website.com/{{tokenId}}","http://www.website.com/{{tokenId}}") ;

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $assetCollectionFactory->create(self::COLLECTION_CODE,null, $pathSolver);
        $erc721 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init();
        $erc721->setTokenId(2);



        /** @var \CsCannon\AssetCollection $collection */

        $collectionName = self::COLLECTION_NAME ;

        $collection->setName($collectionName);
        $collection->setImageUrl("https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png");
        $collection->setDescription("My collection is fantastic");
        $collection->setSolver($pathSolver);

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $assetCollectionFactory->get(self::COLLECTION_CODE);

        $asset = $pathSolver::resolveAsset($collection,$erc721,$contract);









        //$addressEntity = $addressFactory->get($testAddress);
        //$balance = $addressEntity->getBalance();


     //   \CsCannon\Tests\TestManager::registerDataStructure();

    }








}


