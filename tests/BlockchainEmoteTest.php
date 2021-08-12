<?php


use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEmote;
use CsCannon\Blockchains\BlockchainEmoteFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;

class BlockchainEmoteTest extends TestCase
{

    private $kusamaAddress = 'myFirstKusamaAddress';
    private $targetContract = 'myAmazingContract';
    private $sn = '000000EMOTE';
    private $unicode = '1F600';

    public function testEmoteCreation()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123456,
            987654321,
            '0x00Test00',
            '1F600'
        );

        $emoteFactory = new BlockchainEmoteFactory($blockchain);
        $emoteFactory->populateLocal();

        /** @var BlockchainEmote[] $emotes */
        $emotes = $emoteFactory->getEntities();
        $emote = end($emotes);

        $this->assertTrue($emote instanceof BlockchainEmote);

        $this->assertIsString($emote->getEmoteId());
        $this->assertIsString($emote->getTxHash());
        $this->assertIsString($emote->getUnicode());

        $this->assertEquals(strtolower($this->kusamaAddress), $emote->getSourceAddress()->getAddress());

        $contract = $emote->getTargetContract();
        $this->assertNotNull($contract);

        $contractId = $contract->getReference('id')->refValue ?? null;
        $this->assertNotNull($contractId);
        $this->assertEquals($this->targetContract, $contractId);

        /** @var RmrkContractStandard $token */
        $token = $emote->getTargetToken();
        $this->assertNotNull($token);
        $sn = $token->getDisplayStructure();

        $this->assertEquals('sn-'.$this->sn, $sn);

    }



    public function testEmoteActivation()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        // two sends of same unicode on same token sn is inactive (ref on verb 'isActive' need to be false)
        $this->createDesactivatedEmote($blockchain);

        $emoteFactory = new BlockchainEmoteFactory($blockchain);
        $emoteFactory->populateLocal();
        $emotes = $emoteFactory->getEmotes($this->kusamaAddress);

        $result = $emoteFactory->activeEmotes($emotes);

        foreach ($result as $emote){
            $ref = $emote->getReference(BlockchainEmoteFactory::IS_ACTIVE)->refValue ?? null;
            $this->assertNotNull($ref);
            $this->assertFalse($ref);
        }
    }


    public function testEmoteDesactivation()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        // two sends of same unicode on same token sn is inactive (ref on verb 'isActive' need to be false)
        $this->createActivatedEmote($blockchain);

        $emoteFactory = new BlockchainEmoteFactory($blockchain);
        $emoteFactory->populateLocal();
        $emotes = $emoteFactory->getEmotes($this->kusamaAddress);

        $result = $emoteFactory->activeEmotes($emotes);

        foreach ($result as $emote){
            $ref = $emote->getReference(BlockchainEmoteFactory::IS_ACTIVE)->refValue ?? null;
            $this->assertNotNull($ref);
            $this->assertTrue($ref);
        }
    }



    public function testEmoteViewWithActivation()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $blockchain = BlockchainRouting::getBlockchainFromName('kusama');

        $this->createDesactivatedEmote($blockchain);

        $emoteFactory = new BlockchainEmoteFactory($blockchain);
        $emoteFactory->populateLocal();
        // Test search by contract
        $emotes = $emoteFactory->getEmotes($this->targetContract);

        $emotesActivated = $emoteFactory->getViewResponse($emotes);

        foreach ($emotesActivated as $contract => $snArray){
            $this->assertEquals($this->targetContract, $contract);

            foreach ($snArray as $sn => $unicodeArray){
                $this->assertEquals('sn-'.$this->sn, $sn);

                foreach ($unicodeArray as $unicode => $addressArray){
                    $this->assertEquals($this->unicode, $unicode);

                    foreach ($addressArray as $address => $bool){
                        $this->assertEquals(strtolower($this->kusamaAddress), $address);
                        $this->assertFalse($bool);
                    }
                }
            }
        }

    }


    private function createDesactivatedEmote(Blockchain $blockchain)
    {
        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123456,
            987654321,
            '0x00Test00',
            $this->unicode
        );

        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123457,
            987656789,
            '0x00Test01',
            $this->unicode
        );
    }

    private function createActivatedEmote(Blockchain $blockchain)
    {
        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123456,
            987654321,
            '0x00Test00',
            $this->unicode
        );

        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123457,
            987656789,
            '0x00Test01',
            $this->unicode
        );

        $this->createEmote(
            $this->kusamaAddress,
            $this->sn,
            $this->targetContract,
            $blockchain,
            123458,
            987657789,
            '0x00Test01',
            $this->unicode
        );
    }


    private function createEmote(string $source, string $sn, string $contractId, Blockchain $blockchain, int $blockNumber, int $timestamp, string $txHash, string $unicode)
    {
        $emoteFactory = new BlockchainEmoteFactory($blockchain);
//        $event = $blockchain->getEventFactory();

        $address = $blockchain->getAddressFactory()->get($source, true);
        $token = RmrkContractStandard::init(['sn' => $sn]);

        $contract = $blockchain->getContractFactory()->get($contractId, true);
        $blockFactory = new BlockchainBlockFactory($blockchain);

        /** @var BlockchainBlock $block */
        $block = $blockFactory->getOrCreateFromRef(BlockchainBlockFactory::INDEX_SHORTNAME, $blockNumber);

        return $emoteFactory->createEmote(
            $address,
            $contract,
            $txHash,
            $timestamp,
            $block,
            $token,
            $unicode
        );

    }



}