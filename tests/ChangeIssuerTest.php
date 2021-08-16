<?php


use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\ChangeIssuerFactory;
use CsCannon\SandraManager;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;

class ChangeIssuerTest extends TestCase
{

    private $collectionId = 'myAmazingCollection';
    private $sourceAddress = 'firstKusamaAddress';
    private $newIssuer = 'newIssuerKusamaAddress';


    public function testChangeIssuer()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $addressFactory = $blockchain->getAddressFactory();
        $source = $addressFactory->get($this->sourceAddress, true);

        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());

        $collection = $this->createCollection($source, $this->collectionId, $collectionFactory);

        $collectionFactory->populateLocal();


        $this->assertNotNull($collection);
        $this->assertEquals($this->collectionId, $collection->getId());

        /** @var BlockchainAddress[] $owners */
        $owners = $collection->getOwners();
        $owner = end($owners);

        $this->assertEquals(strtolower($this->sourceAddress), $owner->getAddress());

        $newIssuer = $addressFactory->get($this->newIssuer, true);

        $this->createChangeIssuer(
            $blockchain,
            $source,
            $newIssuer,
            $collection->getId(),
            '0xTest0000',
            '123456789',
            123456
        );

        $changeIssuerFactory = new ChangeIssuerFactory($blockchain);
        $changeIssuerFactory->populateLocal();

        $changeIssuers = $changeIssuerFactory->getEntities();
        $changeIssuer = end($changeIssuers);

        $newIssuerAddress = $changeIssuer->getNewIssuer()->getAddress();

        $this->assertEquals(strtolower($this->newIssuer), $newIssuerAddress);
        $this->assertFalse($changeIssuer->isAlreadyReassigned());
        $this->assertEquals($this->collectionId, $changeIssuer->getCollectionId());

        $reassigned = $changeIssuerFactory->reassignCollections();
        $reassigned = end($reassigned);

        $this->assertTrue($reassigned->isAlreadyReassigned());


        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();
        $collection = $collectionFactory->get($this->collectionId);

        /** @var BlockchainAddress[] $owners */
        $owners = $collection->getOwners();
        $owner = end($owners);

        $this->assertEquals($owner->getAddress(), strtolower($this->newIssuer));
    }

    /**
     * @param BlockchainAddress $source
     * @param string $collectionId
     * @param AssetCollectionFactory $collectionFactory
     * @return AssetCollection|null
     */
    private function createCollection(BlockchainAddress $source, string $collectionId, AssetCollectionFactory $collectionFactory)
    {
        $collection = $collectionFactory->create($collectionId, []);
        if(!is_null($collection)){
            $collection->setOwner($source);
        }

        return $collection;
    }


    private function createChangeIssuer(Blockchain $blockchain, BlockchainAddress $source, BlockchainAddress $newIssuer, string $collectionId, string $txId, string $timestamp, int $blockNumber)
    {
        $changeIssuerFactory = new ChangeIssuerFactory($blockchain);

        $blockFactory = new BlockchainBlockFactory($blockchain);
        /** @var BlockchainBlock $block */
        $block = $blockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME, $blockNumber);


        return $changeIssuerFactory->createChangeIssuer(
            $blockchain,
            $source,
            $newIssuer,
            $block,
            $collectionId,
            $txId,
            $timestamp
        );
    }


}