<?php

namespace CsCannon\Blockchains\Polygon\DataSource;


use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\ForeignEntityAdapter;

/**
 * Created by EverdreamSoft.
 * User: Ali Anwar
 * Date: 28.10.22
 * Time: 14:00
 */

/**
 * Test datasource for polygon
 */
class BlockDaemonDataSource extends BlockchainDataSource
{

    public static $apiUrl = 'https://svc.blockdaemon.com/nft/v1/polygon/mainnet';
    public static $apiKey = 'L5QCT3gssIXLMEmKuNSI2j3V4g2ahnquS3h7kChPWQrbKYij';

    /**
     * @throws Exception
     */
    public static function getEvents($contract = null, $batchMax = 100, $offset = 0, $address = null): ForeignEntityAdapter
    {
        if (empty($batchMax) || $batchMax > 100) {
            $batchMax = 100;
        }

        $uri = static::$apiUrl . "events?event_type=transfer&page_size=$batchMax";

        $address = self::getAddressString($address);

        if (!is_null($address)) {
            $uri = $uri . "&wallet_address=$address";
        }

        if (!is_null($contract)) {
            $uri = $uri . "&contract_address=$contract";
        }

        $blockDaemonVocabulary = [
            'id' => 'id',
            'timestamp' => BlockchainEventFactory::EVENT_BLOCK_TIME,
            'from_account' => BlockchainEventFactory::EVENT_SOURCE_ADDRESS,
            'to_account' => BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB,
            ''
        ];

        $formattedForeign = new ForeignEntityAdapter($uri, null, SandraManager::getSandra());
        $formattedForeign->adaptToLocalVocabulary($blockDaemonVocabulary);
        $formattedForeign->populate();

        return $formattedForeign;
    }

    /**
     * @throws Exception
     */
    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {
        throw new Exception("Not implemented");
    }

}
