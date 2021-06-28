<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Asset;
use CsCannon\AssetSolvers\BooSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Orb;
use CsCannon\OrbFactory;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;





final class BalanceBuilderTest extends TestCase
{

    public $scenarioContract =  null ;
    public $scenarioCollection =  null ;



    public function testBalanceBuilding()
    {

        $time = time();


        $counter = 0;
        \CsCannon\Tests\TestManager::initTestDatagraph();

        $kusamaBlockchain = new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain();
        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();

        $mintAddress = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress(\CsCannon\Blockchains\BlockchainAddressFactory::NULL_ADDRESS,true);
        $addressA = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('a',true);
        $addressB = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('b',true);
        $addressC = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('c',true);

        $contractA = \CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory::getContract('c1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());

        $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 1]);
        $t2 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 2]);
        $t3 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 3]);

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($kusamaBlockchain);
        $block1 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,1);
        $block2 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,2);
        $block3 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,3);
        $block4 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,4);
        $block5 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,5);
        $block6 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,6);

        $event = $rmrkEventFactory->create($kusamaBlockchain,$mintAddress,$addressA,$contractA,'fooTx',$time++,$block1,$t1,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);


        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $emptyFactory = clone ($rmrkEventFactory);

        \CsCannon\Tools\BalanceBuilder::buildBalance(clone($emptyFactory));

        $balance = $addressA->getBalance();

        $this->assertEquals(1,$balance->getQuantityForContractToken($contractA,$t1));

        $counter++ ;



        //A send token 1 to B
        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressA,$addressB,$contractA,"fooTx$counter",$time++,$block2,$t1,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $valid2 = $event->subjectConcept ;

        $counter++ ;



        //A double spend to C TX 3
        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressA,$addressC,$contractA,"fooTx$counter",$time++,$block2,$t1,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $invalid1 = $event->subjectConcept ;
        $counter++ ;



        //A mint token 2
        $event = $rmrkEventFactory->create($kusamaBlockchain,$mintAddress,$addressA,$contractA,"fooTx$counter",$time++,$block3,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $valid3 = $event->subjectConcept ;
        $counter++ ;



        //A send token 2 to C
        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressA,$addressC,$contractA,"fooTx$counter",$time++,$block4,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $valid4 = $event->subjectConcept ;
        $counter++ ;



        //C send token 2 back to A
        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressA,$contractA,"fooTx$counter",$time++,$block5,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $valid5 = $event->subjectConcept ;
        $counter++ ;



        //C send token 2 to B double spend
        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressB,$contractA,"fooTx$counter",$time++,$block6,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);
        $invalid2 = $event->subjectConcept ;
        $counter++ ;




        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        \CsCannon\Tools\BalanceBuilder::buildBalance($rmrkEventFactory);




        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $rmrkEventFactory->setFilter(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VALID);

        $rmrkEventFactory->populateLocal();

        $this->assertEquals($rmrkEventFactory->getEntities()[$valid2->idConcept]->subjectConcept,$valid2);
        $this->assertEquals($rmrkEventFactory->getEntities()[$valid3->idConcept]->subjectConcept,$valid3);
        $this->assertEquals($rmrkEventFactory->getEntities()[$valid4->idConcept]->subjectConcept,$valid4);
        $this->assertEquals($rmrkEventFactory->getEntities()[$valid5->idConcept]->subjectConcept,$valid5);
        print_r($rmrkEventFactory->display()->return());


        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $rmrkEventFactory->setFilter(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_INVALID);

        $rmrkEventFactory->populateLocal();
        //$rmrkEventFactory->populateBrotherEntities();
        $this->assertEquals($rmrkEventFactory->getEntities()[$invalid1->idConcept]->subjectConcept,$invalid1);
        $this->assertEquals($rmrkEventFactory->getEntities()[$invalid2->idConcept]->subjectConcept,$invalid2);



        // A should own token 2
        $this->assertEquals(1,$addressA->getBalanceForContract([$contractA])->getQuantityForContractToken($contractA,$t2));


    }

    public function testBalanceBuildingRMRK(){
        $kusamaBlockchain = new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain();
        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        \CsCannon\Tools\BalanceBuilder::resetBalanceBuilder($rmrkEventFactory);

        $contractA = \CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory::getContract('c1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());

        $fooCollectionFactory = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
        $collection = $fooCollectionFactory->create("my foo collection",["maxSupply"=>'2'],LocalSolver::getEntity());


        $contractA->bindToCollection($collection);

        $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 4]);
        $t2 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 5]);
        $t3 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 6]);

        // we mint one more time
        $event = $rmrkEventFactory->create($kusamaBlockchain,$mintAddress,$addressA,$contractA,'fooTx',$time++,$block1,$t1,1);



        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        \CsCannon\Tools\RmrkBalanceBuilder::buildBalance($rmrkEventFactory);


    }




    public function testSetup(){


        \CsCannon\Tests\TestManager::initTestDatagraph();

        $kusamaBlockchain = new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain();
        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();


        $mintAddress = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress(\CsCannon\Blockchains\BlockchainAddressFactory::NULL_ADDRESS,true);
        $addressA = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('a',true);
        $addressB = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('b',true);
        $addressC = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('c',true);

        $contractA = \CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory::getContract('c1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());

        $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 1]);
        $t2 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 2]);
        $t3 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 3]);

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($kusamaBlockchain);
        $block1 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,1);
        $block2 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,2);

        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressB,$contractA,"pending",'10000000',$block1,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING,[]);

        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressB,$contractA,"No data provided",'10000000',$block1,$t2,1);

        $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressB,$contractA,"valid",'10000000',$block1,$t2,1);
        $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VALID,[]);

        for($i=0;$i<10;$i++){

            $event = $rmrkEventFactory->create($kusamaBlockchain,$addressC,$addressB,$contractA,"No data provided $i",'10000000',$block1,$t2,1);
        }

        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        \CsCannon\Tools\BalanceBuilder::flagAllForValidation($rmrkEventFactory);

        $rmrkEventFactory->setFilter(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,0,true);
        $rmrkEventFactory->populateLocal();

        $this->assertCount(0,$rmrkEventFactory->getEntities());

        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $rmrkEventFactory->setFilter(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB,\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING);
        $rmrkEventFactory->populateLocal();

        $this->assertCount(12,$rmrkEventFactory->getEntities());





    }


    public function testRevert(){

        \CsCannon\Tests\TestManager::initTestDatagraph();

        $this->testBalanceBuilding();

        $addressA = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('a',true);
        $addressB = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('b',true);
        $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 1]);
        $t2 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 2]);

        $contractA = \CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory::getContract('c1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());


        //print_r($addressA->getBalance()->getTokenBalanceArray());
        $this->assertEquals(1,$addressA->getBalance()->getQuantityForContractToken($contractA,$t2));
        $this->assertEquals(1,$addressB->getBalance()->getQuantityForContractToken($contractA,$t1));
        \CsCannon\Tools\BalanceBuilder::resetBalanceBuilder(new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory());

        $this->assertEquals(0,$addressA->getBalance()->getQuantityForContractToken($contractA,$t2));


    }

    public function testLotOfData(){

        \CsCannon\Tests\TestManager::initTestDatagraph();

        $kusamaBlockchain = new \CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain();
        $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $rmrkEventFactory2 = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();
        $rmrkEventFactory3 = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();

        \CsCannon\Tools\BalanceBuilder::buildBalance(clone($rmrkEventFactory3));

        $mintAddress = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress(\CsCannon\Blockchains\BlockchainAddressFactory::NULL_ADDRESS,true);
        $addressA = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('a',true);
        $addressB = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('b',true);
        $addressC = \CsCannon\Blockchains\Substrate\Kusama\KusamaAddressFactory::getAddress('c',true);

        $contractA = \CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory::getContract('c1',true,\CsCannon\Blockchains\Interfaces\RmrkContractStandard::getEntity());

        $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 1]);
        $t2 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 2]);
        $t3 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => 3]);

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($kusamaBlockchain);
        $block1 =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,1);

        for($i=0;$i<1000;$i++) {


            $t1 = \CsCannon\Blockchains\Interfaces\RmrkContractStandard::init(['sn' => $i]);
            $event = $rmrkEventFactory->create($kusamaBlockchain, $mintAddress, $addressA, $contractA, 'fooTx', '10000000', $block1, $t1, 1,false);
            $event->setBrotherEntity(\CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_VERB, \CsCannon\Tools\BalanceBuilder::PROCESS_STATUS_PENDING, [],false);

           // $rmrkEventFactory = new \CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory();


        }
        \SandraCore\DatabaseAdapter::commit();
        $emptyFactory = clone($rmrkEventFactory2);

        \CsCannon\Tools\BalanceBuilder::buildBalance(clone($emptyFactory));


         $this->assertEquals(1,1);

    }











}


