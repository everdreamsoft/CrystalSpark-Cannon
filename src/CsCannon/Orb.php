<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;

class Orb
{

    public $contract = null ;
    public $tokenPath = null ;

    public function __construct(BlockchainContract $contract,$tokenPath,$forceInterface = false)
    {


        $this->contract = $contract ;
        $this->tokenPath = $tokenPath ;
    }

    public function getAsset(){




 }

}