<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 10/10/2019
 * Time: 18:23
 */

namespace CsCannon;



use CsCannon\AssetSolvers\AssetSolver;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

class MetadataSolverFactory extends EntityFactory
{

    protected static $isa = 'assetSolver' ;
    protected static $file = 'assetSolverFile' ;



    public function __construct(System $sandra){

        parent::__construct(static::$isa,static::$file,$sandra);


    }

    public function getSolverWithIdentifier($identifier){

        if (!$this->populated){

            $this->populateLocal();
        }

       $thisSolver = $this->last(AssetSolver::IDENTIFIER,$identifier);

        return $thisSolver ;



    }






}