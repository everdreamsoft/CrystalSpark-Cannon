<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-08-15
 * Time: 17:01
 */

namespace CsCannon;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Tests\Displayable;
use SandraCore\Entity;
use SandraCore\EntityFactory;

class Balance
{


    public $contracts = array() ;
    public $contractMap = array() ;

    /**
     *
     * @var OrbFactory
     */
    public $orbFactory ;
    private $orbBuilt = false ;
    public $display ;
    public $address ;

    const LINKED_ADDRESS = 'belongsToAddress';
    const ON_CONTRACT = 'onContract';
    const LAST_BLOCK_UPDATE = 'lastBlockUpdate';
    const BALANCE_ITEM_ID = 'id';


    public function __construct(BlockchainAddress $addressEntity = null)
    {

        $this->address = $addressEntity ;

    }

    public function merge(Balance $balanceToMerge){



        $this->contracts = $this->contracts + $balanceToMerge->contracts;
        $this->contractMap = $this->contractMap + $balanceToMerge->contractMap ;


    }


    public function addContractToken(BlockchainContract $contract,BlockchainContractStandard $contractStandard,$quantity):self{

        $contractChain = $contract->getBlockchain();
        $rawQuantity = $quantity ;
        //print_r($quantity);
        $adaptedQuantity = $contract->getAdaptedDecimals(intval($quantity));
        if ($adaptedQuantity)  $quantity = $adaptedQuantity ;



        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['quantity'] = $quantity;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['adaptedQuantity'] = $adaptedQuantity;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['rawQuantity'] =$rawQuantity ;
        $this->contracts[$contractChain::NAME][$contract->getId()][$contractStandard->getDisplayStructure()]['token'] = $contractStandard;


        $this->contractMap[$contract->getId()] = $contract ;

        return $this ;



    }

    public function getTokenBalance():array {



        $output = array();

        foreach($this->contracts ? $this->contracts : array() as $chainName =>$chain){


            foreach($chain ? $chain : array() as $contractId =>$contracts){

                $newContract = null ;
                $newContract['contract'] = $contractId ;
                $newContract['chain'] = $chainName ;


                foreach($contracts ? $contracts : array() as $tokenComposedId =>$token){

                    //get the token object
                    $tokenObject = $token['token'] ;

                    /** @var BlockchainContractStandard $tokenObject */

                    $newToken =  $tokenObject->specificatorData;
                    $newToken['standard'] = $tokenObject->getStandardName();
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
        foreach($this->contracts ? $this->contracts :array() as $chain){

            //for each contract
            foreach($chain ? $chain :array() as $contractId =>$contracts){

                $newContract = null ;
                $newContract['contract'] = $contractId ;
                $contractEntity = $this->contractMap[$contractId] ;
                //$collections = $contractEntity->getCollections();


                //foreach token
                foreach($contracts ? $contracts : array() as $tokenComposedId =>$token) {


                    /** @var AssetCollection $collectionEntity */

                    $tokenObject = $token['token'] ;
                    //$quantity = $token->
                    //have we found an orb ?
                    if (! is_numeric($contractEntity->entityId)) continue ;
                    if($orbFactory->getOrbsFromContractPath($contractEntity,$tokenObject)){

                        $orbArray = $orbFactory->getOrbsFromContractPath($contractEntity,$tokenObject,$token['quantity']);
                        $orbs[] = $orbArray ;
                    }

                }


            }
        }


        $this->orbFactory = $orbFactory ;

        return $this->orbFactory ;




    }

    public function returnObsByCollections($displayZeroBalance = false):array{


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

                //hide orbs with 0 quantity
                if ($quantity >= 0 or $displayZeroBalance == false){
                    $collection['orbs'][] = $orbDisplay ;

                }




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

    public function getLocalFactory():EntityFactory{



        $factory = new EntityFactory('balanceItem','balanceFile',SandraManager::getSandra());
        $factory->setFilter(self::LINKED_ADDRESS,$this->address);



        return $factory ;


    }

    public function loadFromDatagraph(array $onlyContracts = null){



        $factory = new EntityFactory('balanceItem','balanceFile',SandraManager::getSandra());
        $factory->setFilter(self::LINKED_ADDRESS,$this->address);

        $factory->populateLocal();

        $contractFactory = $this->address->getBlockchain()->getContractFactory();
        $factory->joinFactory(self::ON_CONTRACT,$contractFactory);
        $factory->joinPopulate();

        $balanceEntities = $factory->getEntities();

        foreach ($balanceEntities ? $balanceEntities : array() as $balanceEntity){

            /** @var BlockchainContract $contract */
            $contract = $balanceEntity->getJoinedEntities(self::ON_CONTRACT);
            $contract = reset($contract);

            /** @var BlockchainContractFactory $contractFactory */

            //remove not same chain contracts
            $isa = $contract->system->systemConcept->get('is_a');
            $isaTriplet = $contract->subjectConcept->tripletArray[$isa];
            $actualisaShortname = $contract->factory->entityIsa ;
            $actualisaId = $contract->system->systemConcept->get($actualisaShortname); ;
            if (!in_array($actualisaId,$isaTriplet))
                continue ;

            $quantity =$balanceEntity->get('quantity');
            $token = $contract->getStandard();
            $newToken = clone $token;
            $newToken->setTokenPath($balanceEntity->entityRefs);

            //if (is_array($onlyContracts) && !in_array($contract,$onlyContracts)) continue ;



            $this->addContractToken($contract,$newToken,$quantity);






        }



        return $this ;


    }

    public function saveToDatagraph(BlockchainBlock $lastBlockUpdate){



        $factory = $this->getLocalFactory();

        $factory->populateLocal(100000); //we might have issue if a user has more contract balance than this num

        foreach($this->contracts ? $this->contracts : array() as $chain){


            foreach($chain ? $chain : array() as $contractId =>$contracts){

                $newContract = null ;
                $newContract['contract'] = $contractId ;


                foreach($contracts ? $contracts : array() as $tokenComposedId =>$token){

                    $triplets = [self::LINKED_ADDRESS=>$this->address,
                        self::ON_CONTRACT=>$this->contractMap[$contractId],
                        self::LAST_BLOCK_UPDATE=>$lastBlockUpdate
                    ];



                    //get the token object
                    $tokenObject = $token['token'] ;

                    /** @var BlockchainContractStandard $tokenObject */

                    $newToken =  $tokenObject->specificatorData;
                    $newToken['quantity'] = $token['quantity'];
                    $newToken[self::BALANCE_ITEM_ID] = $this->balanceUniqueId($this->contractMap[$contractId],$tokenObject) ;

                    //does the balance exists in the datagraph ?
                    $existEntity = $factory->first(self::BALANCE_ITEM_ID,$this->balanceUniqueId($this->contractMap[$contractId],$tokenObject));
                    if(!$existEntity){

                        $existEntity = $factory->createNew($newToken,$triplets);
                    }
                    else{

                        $existEntity->createOrUpdateRef('quantity',$token['quantity']);

                        //TODO missing last block

                    }






                    $newContract['tokens'][] = $newToken ;

                }
                $output[] = $newContract ;
            }

        }



        return $factory ;


    }

    public function balanceUniqueId(BlockchainContract $contract, BlockchainContractStandard $standard){

        return $contract->getId().'-'.$standard->getDisplayStructure();

    }

    /**
     * Does the balance own an asset ? Be careful a 0.5 is not considered as false
     * @param Asset $asset
     * @return bool
     */
    public function isOwningAsset(Asset $asset):bool {

        if ($this->quantityForAsset($asset) >= 1) return true ;

        return false ;


    }

    /**
     *
     * @param Asset $asset
     * @return bool
     */
    public function isOwningAssetOrFraction(Asset $asset):bool {

        if ($this->quantityForAsset($asset) > 0) return true ;

        return false ;


    }

    public function quantityForAsset(Asset $asset):int{


        $orbs = $this->orbsForAsset($asset);
        $total = 0 ;

        foreach ($orbs as $orb){

            $total += $this->orbFactory->quantityMap[$orb->orbCode];
        }
        return $total ;

    }

    /**
     * @param Asset $asset
     * @return Orb[]
     */
    public function orbsForAsset(Asset $asset){

        if (!$this->orbFactory) $this->getObs();


        $orbs = $this->orbFactory->getOrbsFromAsset($asset);
        return $orbs ;


    }











}