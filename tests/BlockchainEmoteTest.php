<?php


use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEmoteFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Tests\TestManager;
use PHPUnit\Framework\TestCase;

class BlockchainEmoteTest extends TestCase
{


    public function testEmoteCreation()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        TestManager::initTestDatagraph();

        $emote = $this->createEmote(
            'myFirstKusamaAddress',
            '000000SN',
            'myAmazingContract',
            'kusama',
            123456,
            987654321,
            '0x00Test00',
            '1F600'
        );

        print_r($emote->dumpMeta());

    }



    private function createEmote(string $source, string $sn, string $contractId, string $blockchainName, int $blockNumber, int $timestamp, string $txHash, string $unicode)
    {
        $blockchain = BlockchainRouting::getBlockchainFromName($blockchainName);
        $emoteFactory = new BlockchainEmoteFactory($blockchain);

        $address = $blockchain->getAddressFactory()->get($source);
        $token = RmrkContractStandard::init(['sn' => $sn]);

        $contract = $blockchain->getContractFactory()->get($contractId);
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