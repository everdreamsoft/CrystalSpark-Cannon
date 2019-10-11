<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 10/10/2019
 * Time: 18:23
 */

namespace CsCannon;


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



}