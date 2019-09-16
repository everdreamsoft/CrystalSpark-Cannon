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
    private $contractMap ;



    public function addContractToken(BlockchainContract $contract,BlockchainContractStandard $contractStandard,$quantity){

        $contractChain = $contract->getBlockchain();

        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['quantity'] = $quantity;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['token'] = $contractStandard;


        $this->contractMap[$contract->getId()] = $contract ;

    }

    public function getTokenBalance(){

      foreach($this->contracts as $chain){

          foreach($chain as $contractId =>$contracts){

              $newContract = null ;
              $newContract['contract'] = $contractId ;

              foreach($contracts as $tokenComposedId =>$token) {

                  //get the token object
                  $tokenObject = $token['token'] ;

                  /** @var BlockchainContractStandard $tokenObject */

                  $newToken =  $tokenObject->specificatorData;
                  $newToken['quantity'] = $token['quantity'];


                  $newContract['tokens'][] = $newToken ;

              }
              $output[] = $newContract ;
          }

      }
        return $output ;
    }

    public function getObs(){

        //Has my contract a collection of collections ?

        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();
        $orbs = array();

        //is the contract part of a collection ?
        $orbFactory = new OrbFactory();

        foreach($this->contracts as $chain){

            foreach($chain as $contractId =>$contracts){

                $newContract = null ;
                $newContract['contract'] = $contractId ;
                $contractEntity = $this->contractMap[$contractId] ;
                $collections = $contractEntity->getCollections();



                foreach($contracts as $tokenComposedId =>$token) {


                        /** @var AssetCollection $collectionEntity */

                        $tokenObject = $token['token'] ;
                        //have we found an orb ?
                        if($orbFactory->getOrbsFromContractPath($contractEntity,$tokenObject)){

                            $orbArray = $orbFactory->getOrbsFromContractPath($contractEntity,$tokenObject);


                            $orbs[] = $orbArray ;
                        }




                }


                }
            }

        //a cleaner way would use a filter to the request to point only toward active contracts


echo"hello";

return $orbs ;




    }





}