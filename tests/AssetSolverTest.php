<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Asset;
use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\AssetSolvers\PathPredictableSolver;
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
       $sandra = \CsCannon\SandraManager::getSandra();

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

        $collectionFactory =  new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
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
        $sandra = \CsCannon\SandraManager::getSandra();

        $assetCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $assetCollectionFactory->create($collectionName,null, \CsCannon\AssetSolvers\LocalSolver::getEntity());

        $collection->setName($collectionName);
        $collection->setImageUrl("random");
        $collection->setDescription("Random");
        $collection->setSolver(LocalSolver::getEntity());




        $contractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $contract = $contractFactory->get("@account",true,\CsCannon\Blockchains\Ethereum\Interfaces\ERC721::getEntity());
        $contract->bindToCollection($collection);
        $contractFactory->populateLocal();
       // $standard=$contract->getStandard()->setTokenId(1);



        $contract->setExplicitTokenId(1);



        //An asset that have multiple token Id
        $assetFactory = new \CsCannon\AssetFactory($sandra);
        $myAsset = $assetFactory->create("myUnique",["hello"=>'data'],[$collection],[$contract]);
        $myAsset2 = $assetFactory->create("myUnique2",["hello"=>'data'],[$collection],[$contract]);

        $tokenPathToAssetFactory = new \CsCannon\TokenPathToAssetFactory($sandra);

        $erc721_1 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(1);
        $erc721_2 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(2);
        $erc721_3 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(3);
        $erc721_4 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(4);

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
        $shaban2 = $tokenPathToAssetFactoryV->populateLocal();



        //asset binding
        $myAsset->bindToContractWithMultipleSpecifiers($contract,[$entToSolver,$entToSolver2,$entToSolver1_2]);

        $myAsset2->bindToContractWithMultipleSpecifiers($contract,[$entToSolver2]);


        $tokenPath = $myAsset->getTokenPathForContract($contract);
        $tokenPath2 = $myAsset2->getTokenPathForContract($contract);

        $this->assertCount(2,$tokenPath);



        $this->assertCount(1,$tokenPath2);



        //Now we need to solve
        $mySolver = LocalSolver::getEntity();

        $myOrbFactory = new \CsCannon\OrbFactory();
        $assetFactory = new \CsCannon\AssetFactory(\CsCannon\SandraManager::getSandra());
        $assetFactory->populateLocal();

        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,$erc721_1);

        $this->assertCount(1,$orbs);

        $orbs = $myOrbFactory->getOrbsFromContractPath($contract,$erc721_2);
        $this->assertCount(2,$orbs);

        //var_dump($orbs);





    }







}
