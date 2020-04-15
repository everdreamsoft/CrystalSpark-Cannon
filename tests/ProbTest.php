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





final class ProbTest extends TestCase
{

    public $scenarioContract =  null ;
    public $scenarioCollection =  null ;

    private function getScenarioContract(){

        if (!$this->scenarioContract) {
            $contractFactory = new EthereumContractFactory();
            $contractFactory->populateLocal();
            $this->scenarioContract = $contractFactory->get('0x79986af15539de2db9a5086382daeda917a9cf0c', true, \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init());;
        }

        return $this->scenarioContract ;

    }

    private function getScenarioCollection(){

        $contractFactory = new EthereumContractFactory();

        if (!$this->scenarioCollection) {
            $assetCollection = new \CsCannon\AssetCollectionFactory(\CsCannon\SandraManager::getSandra());
            $this->scenarioCollection = $assetCollection->getOrCreate('CryptoVoxels',null);
        }

        return $this->scenarioCollection ;

    }



    public function testDownlaoding()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();



       $probFactory = new \CsCannon\MetadataProbeFactory();
       $collection = $this->getScenarioCollection();



        $contract = $this->getScenarioContract();
        $contract->setExplicitTokenId(1);

        $contract->bindToCollection($collection);

        $collection->setSolver(LocalSolver::getEntity());

       $probe = $probFactory->create($collection,$contract,'https://www.cryptovoxels.com/p/{tokenId}');
        $probFactory->populateLocal();

        $erc721_1 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(1);
        $erc721_2 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(2);
        $erc721_3 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(3);
        //$erc721_4 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(4);

       $probe->probe($erc721_1,$erc721_2);


       $assetFactory = new \CsCannon\AssetFactory();
       $assetFactory->populateLocal();

       $this->assertCount(2,$assetFactory->getEntities());

        //$probFactory = new \CsCannon\MetadataProbeFactory();
       // $probFactory->populateLocal();

        //$probe = $probFactory->getOrCreate($collection,$contract,'null');




//        $asset1 = LocalSolver::resolveAsset($this->getScenarioCollection(),$erc721_1,$this->getScenarioContract());
       // $asset3 = LocalSolver::resolveAsset($this->getScenarioCollection(),$erc721_3,$this->getScenarioContract());

       //$assetFactory->get()

    }

    public function testOrbSolving()
    {



        $contractAddress = '0x79986af15539de2db9a5086382daeda917a9cf0c';

        $contractFactory = new EthereumContractFactory();
        $contract = $contractFactory->get($contractAddress);
        $ethereumBlockchain = new EthereumBlockchain();

        $ethereumAddressFactory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();
        $address1 = $ethereumAddressFactory->get("myAddy",true);
        $address2 = $ethereumAddressFactory->get("myAddy2",true);

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($ethereumBlockchain);
       $block =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,1);

       $tokenId = 1 ;
       $scenarioImage = 'https://map.cryptovoxels.com/tile/parcel?x=0.09&y=0.11'; //for verification


        $erc721_1 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init($tokenId);

        $ethereumEventF = new EthereumEventFactory();
        $ethereumEventF->create(CsCannon\Blockchains\Ethereum\EthereumBlockchain::getStatic(),$address1,$address2,$contract,"myTx",time(),$block,$erc721_1,1);

        $ethereumEventF = new EthereumEventFactory();
        $ethereumEventF->populateLocal();

       $events =  $ethereumEventF->display()->return();

       $event = reset($events);
       $this->assertEquals($contractAddress,$event['contract']['address']);
       $getTokenData = $event['contract']['token'] ;
       $this->assertEquals($erc721_1->specificatorData,$getTokenData);
       $this->assertEquals($scenarioImage,$event['orbs']['0']['asset']['imgURL']);

        //$asset = LocalSolver::resolveAsset($this->getScenarioCollection(),$erc721_1,$this->getScenarioContract());





    }

    public function testQueueBuilding()
    {

        //There is an issue with hot plug and

       $contract =  $this->getScenarioContract();
       $collection = $this->getScenarioCollection();

        $probFactory = new \CsCannon\MetadataProbeFactory();

        $probFactory->populateLocal();
        $probe = $probFactory->getOrCreate($collection,$contract,'null');


        $this->assertInstanceOf(\CsCannon\MetadataProbe::class,$probe,'Could not laod prob');


        $erc721_3 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(3);
        $erc721_4 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(4);
        $erc721_1 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(1); //this should exist already

        $probe->queue($erc721_3);

       // $probe->queue($erc721_3,$erc721_4,$erc721_1);
        while($probe->executeQueue()) {

        sleep(1);


        }


    }

    public function testQueue()
    {



        $probFactory = new \CsCannon\MetadataProbeFactory();

        $erc721_3 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(3);
        $erc721_4 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(4);
        $erc721_1 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(1); //this should exist already
        $erc721_2 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(2); //this should exist already

        LocalSolver::reloadCollectionItems($this->getScenarioCollection());

        $asset3 = LocalSolver::resolveAsset($this->getScenarioCollection(),$erc721_3,$this->getScenarioContract());



        $this->assertEquals(1,1);




    }

    public function testAutoQueue()
    {

        $erc721_5 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(5);
        $erc721_6 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(6);
        $erc721_7 =  \CsCannon\Blockchains\Ethereum\Interfaces\ERC721::init(7);

        $ethereumAddressFactory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();
        $address1 = $ethereumAddressFactory->get("myAddy",true);
        $address2 = $ethereumAddressFactory->get("myAddy2",true);

        $ethereumBlockchain = new EthereumBlockchain();

        $blockchainBlockFactory = new \CsCannon\Blockchains\BlockchainBlockFactory($ethereumBlockchain);
        $block =  $blockchainBlockFactory->getOrCreateFromRef($blockchainBlockFactory::INDEX_SHORTNAME,1);

        $ethereumEventF = new EthereumEventFactory();

        $ethereumEventF->create(CsCannon\Blockchains\Ethereum\EthereumBlockchain::getStatic(),$address1,$address2,$this->getScenarioContract(),"myTx",time(),$block,$erc721_5,1);
        $ethereumEventF->create(CsCannon\Blockchains\Ethereum\EthereumBlockchain::getStatic(),$address1,$address2,$this->getScenarioContract(),"myTx",time(),$block,$erc721_6,1);
        $ethereumEventF->create(CsCannon\Blockchains\Ethereum\EthereumBlockchain::getStatic(),$address1,$address2,$this->getScenarioContract(),"myTx",time(),$block,$erc721_7,1);



        $ethereumEventF = new EthereumEventFactory();
        $ethereumEventF->populateLocal();

        $events =  $ethereumEventF->display()->return();


        $probFactory = new \CsCannon\MetadataProbeFactory();

        $probFactory->populateLocal();
        $probe = $probFactory->getOrCreate($this->getScenarioCollection(),$this->getScenarioContract(),'null');
        $waitTime = 0 ;


        while ($probe->executeQueue()){





            $now = time();
            $avail = $probe->getNextAvailability();
            $waitTime = $avail - $now ;

            if ($waitTime >0) {
                sleep($waitTime);

            }
        }

        $ethereumEventF = new EthereumEventFactory();
        $ethereumEventF->populateLocal();
        LocalSolver::clean();

        $events =  $ethereumEventF->display()->return();

        print_r($events);







    }








}


