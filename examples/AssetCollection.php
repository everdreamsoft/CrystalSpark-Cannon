<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 31/07/2019
 * Time: 20:17
 */


namespace CsCannon ;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload



use CsCannon ;
use CsCannon\Blockchains\BlockchainContractFactory;
use InnateSkills\LearnFromWeb\LearnFromWeb;
use SandraCore\System;

//$system = new System('',true);
//SandraManager::setSandra($system);


$assetCollection = new AssetCollectionFactory(SandraManager::getSandra());
createLearner();
die();

//$contractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory()
//$contractFactory->populateLocal();


$assetCollection->populateLocal();
//print_r($assetCollection->dumpMeta());
//die();
//$assetCollection->create

// I want to get the first cryptokitties



$ck = $assetCollection->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');
$specifier = new CsCannon\Blockchains\Ethereum\Interfaces\ERC721();
$specifier->setTokenId(1);
$specifier->getFlatTokenPath();





$contractFactory = new CsCannon\Blockchains\Ethereum\EthereumContractFactory();
$contract = $contractFactory->get('0x06012c8cf97bead5deae237070f9587f8e7a266d');

$orbFactory = new OrbFactory();
$orb = OrbFactory::getOrbFromSpecifier($specifier,$contract,$ck);
$orb->getAsset();


 function createLearner(){




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
    $counterpartyContractFactory = new CsCannon\Blockchains\Counterparty\XcpContractFactory();
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

            //dd($data);

            $collectionFactory->createNew($data,$links);

        }

    }



    foreach ($factory->entityArray as $collectionEntity) {


        $collectionCode = $collectionEntity->get('envCode');

        $vocabulary = array(
            'image' => 'image',
            'assetName'=> 'assetName',
            'id'=> 'id',
            'Divisible'=> 'divisible',
        );


        $counterpartyLearnerUrl = "http://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&action=getEnvironment&env=$collectionCode&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&apiv=3&dev=3";
        echo"creating learner BooLearner_".$collectionCode."\n";



        $learner = $weblearner->createOrUpdate("BooLearner_".$collectionCode,$vocabulary, $counterpartyLearnerUrl, 'Environements/$first/Assets', "booCollectionItem_$collectionCode", "BooCollectionFile_$collectionCode", 'assetName', 'assetName');
        $learner->factory->className = 'CsCannon\Asset' ;
        $myCollection = $weblearner->learn($learner,'CsCannon\Asset');
     //   print_r($myCollection);



        $collectionFactory = new AssetCollectionFactory($system);
        $collectionFactory->populateLocal();

        //we need to create tokens

       // print_r($myCollection->entityArray);

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
            $currentAsset =  $assetFactory->first('assetId',"$collectionCode-$contractId");
            // $entityAsset->createOrUpdateRef('assetIdx',"$collectionCode-$assetName");
            if (!$currentAsset) {
                echo "creating new asset  $collectionCode-$contractId \n";


                //$dataArray['name']

                $currentAsset = $assetFactory->createNew($entityAsset->entityRefs, array(AssetFactory::$collectionJoinVerb => $collectionEntity));
                $currentAsset->createOrUpdateRef('assetId',"$collectionCode-$contractId");
                $currentAsset->dumpMeta();
            }
            if($entityAsset instanceof Asset) {

                $currentAsset->bindToContract($entityToken);


            }
            if($entityToken instanceof CsCannon\Blockchains\BlockchainContract && $currentAsset instanceof Asset) {
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










