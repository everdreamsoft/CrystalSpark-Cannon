<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 17:07
 */

namespace CsCannon\Blockchains;


use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\Orb;

abstract class BlockchainContractStandard
{

    public $specificatorArray = array();
    public $specificatorData = array();
    public abstract function resolveAsset(Orb $orb) ;
    public abstract function getStandardName() ;
    public abstract function getDisplayStructure() ;




    public function verifyTokenPath($tokenPath){

        try {

            foreach ($this->specificatorArray as $key => $value) {

                if( !isset($tokenPath[$value])) {
                    throw new \Exception("" .$this->getStandardName() ." token require $value for contract");



                }

                $this->specificatorData[$value] = $tokenPath[$value] ;

            }
        }
        catch (\Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            die();
        }


    }

    public function setTokenPath($tokenPath){

        $this->verifyTokenPath($tokenPath);

    }




}