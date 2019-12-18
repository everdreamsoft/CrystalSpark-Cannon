<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\AssetSolvers\AssetSolver;
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







}
