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
use CsCannon\Tests\Displayable;
use SandraCore\Entity;

class Balance Implements Displayable
{


    public $contracts ;
    private $contractMap ;
    public $orbFactory ;
    private $orbBuilt = false ;
    public $display ;

    public function __construct()
    {

        $this->display = new DisplayManager($this);

    }


    public function addContractToken(BlockchainContract $contract,BlockchainContractStandard $contractStandard,$quantity){

        $contractChain = $contract->getBlockchain();
        //print_r($quantity);

        //echo"<br> \n getting contract ID  ".$contract->getId(). $quantity;

        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['quantity'] = $quantity;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['token'] = $contractStandard;


        $this->contractMap[$contract->getId()] = $contract ;



    }

    public function getTokenBalance(){


//print_r($this->contractMap);
//die("deado");
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

    public function getObs():OrbFactory{



        //Has my contract a collection of collections ?

        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();
        $orbs = array();

        //is the contract part of a collection ?
        $orbFactory = new OrbFactory();

        //for each blockchain
        foreach($this->contracts as $chain){

            //for each contract
            foreach($chain as $contractId =>$contracts){

                $newContract = null ;
                $newContract['contract'] = $contractId ;
                $contractEntity = $this->contractMap[$contractId] ;
                $collections = $contractEntity->getCollections();


                //foreach token
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


$this->orbFactory = $orbFactory ;

return $this->orbFactory ;




    }

    public function returnObsByCollections():array{


      $factory = $this->getObs();
      $output = array();

      if (!is_array($factory->instanceCollectionMap)) return $output ;

       foreach ($factory->instanceCollectionMap as $collectionId => $orbs){

           /** @var Orb $firstOrb */
           $firstOrb = reset($orbs);
           $collection = $firstOrb->assetCollection->getDefaultDisplay();


           foreach ($orbs as $index => $orb) {

               /** @var BlockchainContract $contract */
               $contract = $orb->contract ;

               /** @var BlockchainContractStandard $token */
               $token = $orb->tokenSpecifier ;

               /** @var Asset $asset */
               $asset = $orb->asset ;

               $contractChain = $contract->getBlockchain();

               $quantity = $this->contracts[$contractChain::NAME][$contract->getId()][$token->getDisplayStructure()]['quantity'] ;

               /** @var Orb $orb */
               $orbDisplay['contract'] = $contract->getId();
               $orbDisplay['chain'] = $contractChain::NAME;

               $orbDisplay['token'] = $token->specificatorData ;
               $orbDisplay['token']['standard'] = $token->getStandardName();
               $orbDisplay['quantity'] = $quantity ;
               $orbDisplay['asset']['image'] = $asset->imageUrl ;



               $collection['orbs'][] = $orbDisplay ;
           }

           $output['collections'][] = $collection ;

           }

       //die(print_r(json_encode($output)));

        return $output ;

       }

    function print_array($array,$depth=1,$indentation=0){
        if (is_array($array)){
            echo "Array(\n";
            foreach ($array as $key=>$value){
                if(is_array($value)){
                    if($depth){
                        echo "max depth reached.";
                    }
                    else{
                        for($i=0;$i<$indentation;$i++){
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                        echo $key."=Array(";
                        $this->print_array($value,$depth-1,$indentation+1);
                        for($i=0;$i<$indentation;$i++){
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                        echo ");";
                    }
                }
                else{
                    for($i=0;$i<$indentation;$i++){
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
                    }
                    echo $key."=>".$value."\n";
                }
            }
            echo ");\n";
        }
        else{
            echo "It is not an array\n";
        }
    }

public function getContractMap(){

        return $this->contractMap ;

}











}