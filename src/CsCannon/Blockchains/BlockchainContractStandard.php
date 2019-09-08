<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 17:07
 */

namespace CsCannon\Blockchains;


abstract class BlockchainContractStandard
{

    public $specificatorArray = array();
    public abstract function getStandardName() ;


    public function verifyTokenPath($tokenPath){

        try {

            foreach ($this->specificatorArray as $key => $value) {

                if( !isset($tokenPath[$key])) {
                    throw new \Exception("Orb require" .$this->getStandardName() ."for contract");

                }

            }
        }
        catch (\Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }


    }

    public function setToken($tokenPath){

        $this->verifyTokenPath($tokenPath);

    }


}