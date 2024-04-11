<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\AssetCollectionFactory;
use CsCannon\AssetSolvers\PathPredictableSolver;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Polygon\DataSource\AlchemyDataSource;
use CsCannon\Blockchains\Polygon\PolygonAddress;
use CsCannon\Blockchains\Polygon\PolygonAddressFactory;
use CsCannon\Blockchains\Polygon\PolygonBlockchain;
use CsCannon\Blockchains\Polygon\PolygonContractFactory;
use CsCannon\SandraManager;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;
use SandraCore\Entity;
use SandraCore\EntityFactory;

final class PolygonIntegrationTest extends TestCase
{

    /**
     * @throws Exception
     */
    public function testIntegration()
    {
        echo "Starting polygon integration tests, this will flush and create new phpunit_ env for testing...\n";

        TestManager::initTestDatagraph(true);

        $this->activeChainValidations();
        $this->contractValidations();
        $this->addressValidations();
        $this->testDataSource();

    }


    private function activeChainValidations()
    {

        echo "Validating polygon as an active blockchain \n";

        $this->chainCreation();
        $chains = $this->getChain();

        $this->assertCount(1, $chains, "Chain not created");

        /** @var Entity $polygon */
        $polygon = reset($chains);

        $verb = SandraManager::getSandra()->conceptFactory->getConceptFromShortnameOrId("onBlockchain");
        $this->assertNotNull($verb, "onBlockchain not found");
        $this->assertNotEmpty($polygon->subjectConcept->tripletArray, "Triplet array empty");
        $targetConcept = reset($polygon->subjectConcept->tripletArray[$verb->idConcept]);
        $this->assertNotNull($targetConcept, "Target not found");
        $target = SandraManager::getSandra()->systemConcept->getSCS(reset($polygon->subjectConcept->tripletArray[$verb->idConcept]));
        $this->assertEquals(PolygonBlockchain::NAME, $target, "Invalid onblockchain link");
        $this->assertEquals(PolygonBlockchain::NAME, $polygon->get("blockchain"), "Invalid blockchain ref");

    }

    private function contractValidations()
    {

        echo "Validating polygon as an contracts \n";

        $this->contractCreation();
        $contracts = $this->getContract();

        $this->assertCount(1, $contracts, "Contract not created");

        /** @var Entity $polygon */
        $polygon = reset($contracts);

        $verb = SandraManager::getSandra()->conceptFactory->getConceptFromShortnameOrId("onBlockchain");
        $this->assertNotNull($verb, "onBlockchain not found");
        $this->assertNotEmpty($polygon->subjectConcept->tripletArray, "Triplet array empty");
        $targetConcept = reset($polygon->subjectConcept->tripletArray[$verb->idConcept]);
        $this->assertNotNull($targetConcept, "Target not found");
        $target = SandraManager::getSandra()->systemConcept->getSCS(reset($polygon->subjectConcept->tripletArray[$verb->idConcept]));
        $this->assertEquals(PolygonBlockchain::NAME, $target, "Invalid onblockchain link");
        $this->assertEquals("0x9CD2A10b03a5D0897ae8630DA11fC53E50A645Fc", $polygon->get("id"), "Invalid id");

    }

    private function addressValidations()
    {

        echo "Validating polygon as an addresses \n";

        $this->addressCreation();
        $addresses = $this->getAddress();
        $this->assertCount(1, $addresses, "Contract not created");
        /** @var Entity $polygon */
        $polygon = reset($addresses);

        $verb = SandraManager::getSandra()->conceptFactory->getConceptFromShortnameOrId("onBlockchain");
        $this->assertNotNull($verb, "onBlockchain not found");
        $this->assertNotEmpty($polygon->subjectConcept->tripletArray, "Triplet array empty");
        $targetConcept = reset($polygon->subjectConcept->tripletArray[$verb->idConcept]);
        $this->assertNotNull($targetConcept, "Target not found");
        $target = SandraManager::getSandra()->systemConcept->getSCS(reset($polygon->subjectConcept->tripletArray[$verb->idConcept]));
        $this->assertEquals(PolygonBlockchain::NAME, $target, "Invalid onblockchain link");
        $this->assertEquals("0x16738e8c43c4a92b4b84d7da811c913cf0d8c4bc", $polygon->get("address"), "Invalid address");

    }

    private function chainCreation()
    {

        $activeBlockchainData = new EntityFactory('activeBlockchain', 'activeBlockchainFile', SandraManager::getSandra());
        $activeBlockchainData->populateLocal();
        $data['blockchain'] = PolygonBlockchain::NAME;
        $data['explorerTx'] = PolygonBlockchain::getNetworkData("mainnet", 'explorerTx');
        $activeBlockchainData->createOrUpdateOnReference('blockchain', PolygonBlockchain::NAME, $data, ['onBlockchain' => PolygonBlockchain::NAME]);
    }

    private function getChain(): array
    {
        $activeBlockchainData = new EntityFactory('activeBlockchain', 'activeBlockchainFile', SandraManager::getSandra());
        $activeBlockchainData->populateFromSearchResults(PolygonBlockchain::NAME, "blockchain");
        $activeBlockchainData->populateBrotherEntities("onBlockchain");
        $activeBlockchainData->getTriplets();
        return $activeBlockchainData->getEntities();
    }

    private function contractCreation()
    {
        $contractFactory = new PolygonContractFactory();
        $contract = $contractFactory->get('0x9CD2A10b03a5D0897ae8630DA11fC53E50A645Fc', true, ERC721::init());
        $assetCollectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionEntity = $assetCollectionFactory->getOrCreate("polygonTestCollection");
        $collectionEntity->setName("Matic Test Collection");
        $collectionEntity->setDescription("We could update this text in the future");
        $collectionEntity->setImageUrl("https://tokenpost.com/assets/uploads/20190502ac6b32835ae4c139a.png");
        $collectionEntity->setSolver(PathPredictableSolver::getEntity('https://tokenpost.com/assets/uploads/20190502ac6b32835ae4c139a.png', 'https://matic.network/', 'https://tokenpost.com/assets/uploads/20190502ac6b32835ae4c139a.png'));
        $contract->bindToCollection($collectionEntity);
    }

    private function getContract(): array
    {
        $contractFactory = new PolygonContractFactory();
        $contractFactory->populateFromSearchResults("0x9CD2A10b03a5D0897ae8630DA11fC53E50A645Fc", "id");
        $contractFactory->populateBrotherEntities();
        $contractFactory->getTriplets();
        return $contractFactory->getEntities();
    }

    private function addressCreation()
    {
        $addressFactory = new PolygonAddressFactory();
        $addressFactory->get("0x16738e8c43c4a92b4b84d7da811c913cf0d8c4bc", true);
    }

    private function getAddress(): array
    {
        $addressFactory = new PolygonAddressFactory();
        $addressFactory->populateFromSearchResults("0x16738e8c43c4a92b4b84d7da811c913cf0d8c4bc", "address");
        $addressFactory->populateBrotherEntities();
        $addressFactory->getTriplets();
        return $addressFactory->getEntities();
    }

    private function testDataSource()
    {

        echo "Validating polygon alchecmy datasource\n";

        $addressFactory = new PolygonAddressFactory();
        $addressFactory->populateFromSearchResults("0x16738e8c43c4a92b4b84d7da811c913cf0d8c4bc", "address");
        $addressFactory->populateBrotherEntities();
        $addressFactory->getTriplets();
        $entities = $addressFactory->getEntities();
        $address = reset($entities);

        $this->assertNotNull($address, "Address not found");

        if ($address) {

            $datasource = new AlchemyDataSource(AlchemyDataSource::NETWORK_MUMBAI);
            AlchemyDataSource::setApiKey("2U3ERhEqMX2qjwpNjadYJTCCDWgQRCiK");

            /** @var PolygonAddress $address */
            $address->setDataSource($datasource);
            $polygonBalance = $address->getBalance(1, 0);

            $polygonBalance->getTokenBalanceArray();

            $this->assertNotEmpty($polygonBalance->getTokenBalanceArray(), "Empty token balance array");
            $this->assertNotNull($polygonBalance->getTokenBalanceArray()[0]["tokens"], "Tokens not found");

        }

    }

}
