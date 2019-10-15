<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 10:22
 */

namespace CsCannon\AssetSolvers;


use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use CsCannon\SandraManager;
use InnateSkills\LearnFromWeb\LearnFromWeb;
use SandraCore\EntityFactory;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;

class BooSolver extends LocalSolver
{



    public static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract):array{

        //if was never initialized
        if (self::getLastUpdate() == null)self::update();

        return parent::resolveAsset($assetCollection,  $specifier,  $contract);




    }

    protected static function updateSolver(){




            $system =  SandraManager::getSandra();
            $weblearner = new LearnFromWeb($system);

            $url = 'http://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&action=getEnvironments&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&apiv=3&dev=3';

            $vocabulary = array(
                'envCode' => 'envCode',
                'Title'=> 'Title',
            );

            $learner = $weblearner->createOrUpdate("BooCollectionsLearner",$vocabulary,$url,'Environements','booEnv','BooFile','envCode','envCode');
            $weblearner->learn($learner);

            //dd($learner->factory);
            //we are going to build a learner for each counterparty collections




            echo"Before cycling \n";

            error_reporting(0);

            //die();

            $weblearner = new LearnFromWeb($system);

            $factory = $weblearner->getFactoryFromLearnerName('BooCollectionsLearner');
            $tokenCreated = 0 ;
            $counterpartyContractFactory = new XcpContractFactory();
            $counterpartyContractFactory->populateLocal();
            // dd($counterpartyContractFactory);

            $assetFactory = new AssetFactory(SandraManager::getSandra());
            $assetFactory->populateLocal();
            //$assetFactory->getRefMap('assetId');

            //dd($assetFactory);

            $collectionFactory = new AssetCollectionFactory($system);
            $collectionFactory->populateLocal();
            //we build the collection list
            foreach ($factory->entityArray as $booCollection){

                $envCode = $booCollection->get('envCode');
                $collectionEntity = $collectionFactory->first('collectionId',$envCode);

                if(is_null($collectionEntity)){

                    $data['title'] = $booCollection->get('Title');
                    $data['masterCurrency'] = $booCollection->get('MasterCurrency');
                    $data['symbol'] = $booCollection->get('ticker');

                    $data['name'] = $booCollection->get('Title');
                    $data['symbol'] = $booCollection->get('ticker');
                    $data['bundleId'] = $booCollection->get('bundleId');
                    $data['imageUrl'] = $booCollection->get('bannerImage');
                    $data['description'] = $booCollection->get('description');
                    $data['wideIcon'] = $booCollection->get('wideIcon');
                    $data['wideIcon'] = $booCollection->get('wideIcon');

                    $data['collectionId'] = $booCollection->get('envCode');

                    $links['hasSource'] = 'BookOfOrbs';

                    $collectionFactory->create($envCode,$data,BooSolver::getEntity());

                }

            }



            foreach ($factory->entityArray as $collectionEntity) {


                $collectionCode = $collectionEntity->get('envCode');

                $vocabulary = array(
                    'image' => AssetFactory::IMAGE_URL,
                    'assetName'=> 'assetName',
                    'id'=> AssetFactory::ID,
                    'Divisible'=> 'divisible',
                );


                $counterpartyLearnerUrl = "http://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&action=getEnvironment&env=$collectionCode&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&apiv=3&dev=3";
                echo"creating learner BooLearner_".$collectionCode."\n";

                $foreignFacotry = new ForeignEntityAdapter($counterpartyLearnerUrl,'Environements/$first/Assets',$system);
                $foreignFacotry->adaptToLocalVocabulary($vocabulary);
                $foreignFacotry->populate();
                $myCollection = $foreignFacotry;


                $collectionFactory = new AssetCollectionFactory($system);
                $collectionFactory->populateLocal();

                //we need to create contracts
                foreach ($myCollection->entityArray as $entityAsset){


                    $tokenCreated++ ;
                    $contractId = $entityAsset->get('assetName');

                    echo" - \n ".  $entityAsset->get('assetName');

                    if (!$contractId) continue ;


                    $collectionEntity = $collectionFactory->first('collectionId',$collectionCode);

                    if (is_null($collectionEntity)){

                        die("error unexisting $collectionCode");


                    }


                    if(isset($tokenIndex[$contractId])){
                        $entityToken = $tokenIndex[$contractId] ;
                    }
                    else {
                        $entityToken = $counterpartyContractFactory->get($contractId, true);


                    }
                    //does the asset exists ?
                    $currentAsset =  $assetFactory->first(AssetFactory::ID,"$collectionCode-$contractId");
                    // $entityAsset->createOrUpdateRef('assetIdx',"$collectionCode-$assetName");
                    if (!$currentAsset) {
                        echo "creating new asset  $collectionCode-$contractId \n";


                        $currentAsset = $assetFactory->createNew($entityAsset->entityRefs, array(AssetFactory::$collectionJoinVerb => $collectionEntity));
                        $currentAsset->createOrUpdateRef(AssetFactory::ID,"$collectionCode-$contractId");

                    }



                    $currentAsset->setBrotherEntity(AssetFactory::$tokenJoinVerb,$entityToken,null);
                    echo" asset : ".$currentAsset->subjectConcept->idConcept ." bindeded to contract:". $entityToken->subjectConcept->idConcept ."\n";



                    if($entityToken instanceof BlockchainContract && $currentAsset instanceof Asset) {
                        echo "binding contract".$entityToken->getId();
                        $entityToken->bindToAsset($currentAsset);
                        echo $entityToken->entityId." token binded $collectionCode-$contractId to asset $currentAsset->entityId  \n";

                        $entityToken->setBrotherEntity(BlockchainContractFactory::JOIN_COLLECTION,$collectionEntity,null);

                    }

                    $tokenIndex[$contractId] = $entityToken;


                }


            }
            echo("created".$tokenCreated);
            print_r($counterpartyContractFactory->return2dArray());






        }







}