<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-08-15
 * Time: 17:01
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainToken;
use SandraCore\Entity;

class Balance
{

    public $tokens ;
    public $contracts ;



    public function addContractToken(BlockchainContract $contract,$tokenId,$quantity){

        $contractChain = $contract->getBlockchain();

        $this->contracts[$contractChain::NAME][$contract->getId()][$tokenId] = $quantity;




    }

    public function get(){

      return $this->contracts ;




    }



}