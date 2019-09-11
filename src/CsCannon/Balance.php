<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-08-15
 * Time: 17:01
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use SandraCore\Entity;

class Balance
{

    public $tokens ;
    public $contracts ;



    public function addContractToken(BlockchainContract $contract,BlockchainContractStandard $contractStandard,$quantity){

        $contractChain = $contract->getBlockchain();

        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['quantity'] = $quantity;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['token'] = $contractStandard;

    }

    public function getTokenBalance(){

      foreach($this->contracts as $chain){



          foreach($chain as $contractId =>$contracts){
              foreach($contracts as $tokenComposedId =>$token) {

                  //get the token object
                  $tokenObject = $token['token'] ;

                  /** @var BlockchainContractStandard $tokenObject */

                  $newToken =  $tokenObject->specificatorData;
                  $newToken['quantity'] = $token['quantity'];


                  $output[$contractId][] = $newToken ;


              }
          }




      }



        return $output ;
    }





}