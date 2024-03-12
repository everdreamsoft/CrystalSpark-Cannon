<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use CsCannon\Blockchains\Polygon\PolygonBlockchain;
use CsCannon\SandraManager;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;
use SandraCore\Entity;
use SandraCore\EntityFactory;

final class PolygonIntegrationTest extends TestCase
{

    public function testIntegration()
    {
        echo "Starting polygon integration tests, this will flush and create new phpunit_ env for testing...\n";
        TestManager::initTestDatagraph(true);

        $this->chainCreation();
        $chains = $this->validateChain();

        $this->assertCount(1, $chains, "Chain not created");

        /** @var Entity $polygon */
        $polygon = reset($chains);


        $onBlockchain = $polygon->getBrotherEntity("onBlockchain");


    }

    private function chainCreation()
    {
        echo "Adding polygon as an active blockchain \n";
        $activeBlockchainData = new EntityFactory('activeBlockchain', 'activeBlockchainFile', SandraManager::getSandra());
        $activeBlockchainData->populateLocal();
        $data['blockchain'] = PolygonBlockchain::NAME;
        $data['explorerTx'] = PolygonBlockchain::getNetworkData("mainnet", 'explorerTx');
        $activeBlockchainData->createOrUpdateOnReference('blockchain', PolygonBlockchain::NAME, $data, ['onBlockchain' => PolygonBlockchain::NAME]);
    }

    private function validateChain(): array
    {
        echo "Getting polygon as an active blockchain \n";
        $activeBlockchainData = new EntityFactory('activeBlockchain', 'activeBlockchainFile', SandraManager::getSandra());
        $activeBlockchainData->populateFromSearchResults(PolygonBlockchain::NAME, "blockchain");
        $activeBlockchainData->populateBrotherEntities();
        return $activeBlockchainData->getEntities();
    }

    private function contractCreation()
    {

    }

    private function eventCreation()
    {

    }

    private function addressCreation()
    {

    }

    public function testDataSource()
    {

    }

}
