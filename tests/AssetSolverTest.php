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







}
