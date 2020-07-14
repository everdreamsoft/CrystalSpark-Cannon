<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Asset;
use CsCannon\AssetFactory;
use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\AssetSolvers\PathPredictableSolver;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Orb;
use CsCannon\OrbFactory;
use CsCannon\SandraManager;
use PHPUnit\Framework\TestCase;





final class AssetSolverTest extends TestCase
{

    public function getSupportedSolvers():array{

        $solversId[] = \CsCannon\AssetSolvers\BooSolver::getEntity()->getSolverIdentifier();
        $solversId[] = \CsCannon\AssetSolvers\PathPredictableSolver::getEntity()->getSolverIdentifier();

        return $solversId ;




    }

   public function testLoadAssetSolver(){

       ini_set('display_errors', 1);
       ini_set('display_startup_errors', 1);
       error_reporting(E_ALL);

       \CsCannon\Tests\TestManager::initTestDatagraph();
       $sandra = SandraManager::getSandra();

       \CsCannon\AssetSolvers\BooSolver::getEntity();
       \CsCannon\AssetSolvers\DefaultEthereumSolver::getEntity();
       PathPredictableSolver::getEntity();

       $assetSolverFactory = new \CsCannon\MetadataSolverFactory($sandra);
      $solvers = $this->getSupportedSolvers();


       foreach ($solvers as  $solverId) {

           $solverEntity = $assetSolverFactory->getSolverWithIdentifier($solverId);
           $this->assertInstanceOf(AssetSolver::class,$solverEntity);
           $this->assertEquals($solverId,$solverEntity->getSolverIdentifier());
           }


   }

    public function testSetSolver(){

        $collectionFactory =  new \CsCannon\AssetCollectionFactory(SandraManager::getSandra());
        $predictableSolver = PathPredictableSolver::getEntity();
        $noParamSolver = LocalSolver::getEntity();
        $noparamSolverOverrided = clone $noParamSolver ;
        $invalidSolver = clone $predictableSolver ;
        $invalidSolverFiltered = clone $predictableSolver ;
        $validSolver = clone $predictableSolver ;
        $overridedAndFilteredSolver = clone $predictableSolver ;

        $invalidSolver->setAdditionalParam(['youhoo'=>"yahaaa"]);
        $noparamSolverOverrided->setAdditionalParam(['youhoo'=>"yahaaa"]);
        $invalidSolver->setAdditionalParam(['youhoo'=>"yahaaa"]);
        $invalidSolverFiltered->filterAndSetParam(['youhoo'=>"yahaaa"]);
        $validSolver->setAdditionalParam([Asset::IMAGE_URL=>"http://my", Asset::METADATA_URL=>'url']);
        $overridedAndFilteredSolver->filterAndSetParam([Asset::IMAGE_URL=>"http://my", Asset::METADATA_URL=>'url','unusefulData'=>'notUseful']);

        //has it been correctely filtered
        $this->assertEquals($overridedAndFilteredSolver->getAdditionalParam(),$validSolver->getAdditionalParam());

        $this->assertTrue($noParamSolver->validate());
        $this->assertTrue($noparamSolverOverrided->validate());
        $this->assertTrue($overridedAndFilteredSolver->validate());
        $this->assertTrue($overridedAndFilteredSolver->validate());

        $this->assertFalse($invalidSolver->validate());
        $this->assertFalse($invalidSolverFiltered->validate());


        $collectionFactory->create('yahaa',null,$validSolver);




    }

    public function testAssetToTokenPathTest(){



        $collectionName = 'testCollection';
        $sandra = SandraManager::getSandra();

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(SandraManager::getSandra());
        $collection = $assetCollectionFactory->create($collectionName,null, \CsCannon\AssetSolvers\LocalSolver::getEntity());

        $collection->setName($collectionName);
        $collection->setImageUrl("random");
        $collection->setDescription("Random");
        $collection->setSolver(LocalSolver::getEntity());




        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get("@account",true, ERC721::getEntity());
        $contract->bindToCollection($collection);
        $contractFactory->populateLocal();
       // $standard=$contract->getStandard()->setTokenId(1);



        $contract->setExplicitTokenId(1);



        //An asset that have multiple token Id
        $assetFactory = new AssetFactory($sandra);
        $myAsset = $assetFactory->create("myUnique",["hello"=>'data'],[$collection],[$contract]);
        $myAsset2 = $assetFactory->create("myUnique2",["hello"=>'data'],[$collection],[$contract]);

        $tokenPathToAssetFactory = new \CsCannon\TokenPathToAssetFactory($sandra);

        $erc721_1 =  ERC721::init(1);
        $erc721_2 =  ERC721::init(2);
        $erc721_3 =  ERC721::init(3);
        $erc721_4 =  ERC721::init(4);

        $entToSolver = $tokenPathToAssetFactory->create($erc721_1);
        $entToSolver2 = $tokenPathToAssetFactory->create($erc721_2);
        $entToSolver1_2 =$tokenPathToAssetFactory->create($erc721_1); //duplicata for test
        $entToSolver3 =$tokenPathToAssetFactory->create($erc721_3);

        $entSolverTest3 = $tokenPathToAssetFactory->getOrCreate($erc721_3);
        $entSolverTest4 = $tokenPathToAssetFactory->getOrCreate($erc721_4);


        $this->assertEquals($entToSolver->subjectConcept->idConcept,$entToSolver1_2->subjectConcept->idConcept);
        $this->assertCount(4,$tokenPathToAssetFactory->getEntities());



        //At this point we should have 2 entity path
        $tokenPathToAssetFactoryV = new \CsCannon\TokenPathToAssetFactory($sandra);
        $tokenPathToAssetFactoryV->populateLocal();



        //asset binding
        $myAsset->bindToContractWithMultipleSpecifiers($contract,[$entToSolver,$entToSolver2,$entToSolver1_2]);

        $myAsset2->bindToContractWithMultipleSpecifiers($contract,[$entToSolver2]);


        $tokenPath = $myAsset->getTokenPathForContract($contract);
        $tokenPath2 = $myAsset2->getTokenPathForContract($contract);

        $this->assertCount(2,$tokenPath);



        $this->assertCount(1,$tokenPath2);



        //Now we need to solve
        $mySolver = LocalSolver::getEntity();

        $myOrbFactory = new OrbFactory();
        $assetFactory = new AssetFactory(SandraManager::getSandra());
        $assetFactory->populateLocal();

        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,$erc721_1);

        $this->assertCount(1,$orbs);

        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,$erc721_2);
        $this->assertCount(2,$orbs);

        //var_dump($orbs);

    }

    public function testAssetToTokenBalance(){

        $collectionName = 'testCollection';
        $sandra = SandraManager::getSandra();


        $assetFactory = new AssetFactory(SandraManager::getSandra());
        $assetFactory->populateLocal();

        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contractFactory->populateLocal();
        $contract = $contractFactory->get("@account");


        $myOrbFactory = new OrbFactory();
        $assetFactory = new AssetFactory(SandraManager::getSandra());

        // as defined previously we should have 2 asset linked to token id 1
        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,ERC721::init(1));
        $bindedAssetCount = 2 ;


        /** @var  $orb Orb */
        $orb = reset($orbs);


        $addressFactory = new EthereumAddressFactory();
        $address = $addressFactory->get("testAddress",true);
        $address->setDataSource(new DatagraphSource());
        $balance = $address->getBalance();

        $unitPerToken = 2 ;

        //we create a virtual balance
        for ($i=1;$i<5;$i++) {
            $balance->addContractToken($contract,ERC721::init($i),$unitPerToken);

        }
        $blockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory(new \CsCannon\Blockchains\Ethereum\EthereumBlockchain());
        $block = $blockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME,1);
        $balance->saveToDatagraph($block);

        //asset to find
        $asset = $orb->asset ;

        $assetQuantity = $balance->quantityForAsset($asset);
        //we should have two of this $asset based on 2 tokens with a quantity $unitPerToken ;
        $this->assertEquals($bindedAssetCount*$unitPerToken,$assetQuantity);

        $balance = $address->getBalance($orb);

        $orbList =  $balance->orbsForAsset($asset);

        $this->assertCount($bindedAssetCount,$orbList);

        $this->assertTrue($balance->isOwningAsset($asset));


       $i = 1 ;


    }







}
