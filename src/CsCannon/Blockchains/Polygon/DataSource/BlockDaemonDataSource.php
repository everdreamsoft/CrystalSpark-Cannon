<?php

namespace CsCannon\Blockchains\Polygon\DataSource;


use CsCannon\AssetCollectionFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Ethereum\EthereumContract;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
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
        return self::getBalanceForContract($address, array(), $limit, $offset);
    }

    public static function getBalanceForContract(BlockchainAddress $address, array $contracts, $limit, $offset): Balance
    {

        if ($limit > 100) $limit = 100;

        $wallet_address = $address->getAddress();
        $tokens = [];

        if (!empty($contracts)) {
            foreach ($contracts as $contract) {
                $data = self::gatherData(static::$apiUrl . "/assets?wallet_address=$wallet_address&page_size=$limit&contract_address=" . $contract->getId());
                $tokens = array_merge_recursive($tokens, $data);
            }
        } else {
            $tokens = self::gatherData(static::$apiUrl . "/assets?wallet_address=$wallet_address&page_size=$limit");
        }

        $foreignAdapter = new ForeignEntityAdapter(
            "",
            "data",
            SandraManager::getSandra(),
            null,
            $tokens
        );

        $assetVocabulary = array(
            'image_url' => 'image',
            'assetName' => 'assetName',
            'name' => 'name',
        );

        // $foreignAdapter->flatSubEntity('contract_address','contract');
        $foreignAdapter->adaptToLocalVocabulary($assetVocabulary);
        $foreignAdapter->populate();

        $contractFactory = new EthereumContractFactory();
        $contractFactory->populateFromSearchResults(self::$contractMap, BlockchainContractFactory::MAIN_IDENTIFIER);

        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();

        self::$localCollections = $collectionFactory;
        $balance = new Balance();

        //BlockDaemon API ruleset
        foreach ($foreignAdapter->entityArray as $entity) {
            // Contract data
            /** @var ForeignEntity $entity */
            $contractAddress = $entity->get('contract_address');
            /** @var EthereumContract $ethContract */
            $ethContract = $contractFactory->get($contractAddress);

            $contractStandard = UnknownStandard::init();
            $standard = ERC721::init();
            // Token data
            $standard->setTokenId($entity->get('token_id'));
            $balance->addContractToken($ethContract, $standard, 1);
        }
        $balance->address = $address;
        return $balance;
    }

    public function createCollectionFromOS($openseaEntity)
    {
    }

    public static function setApiKey($key)
    {
        static::$apiKey = $key;
    }


    /**
     * @throws Exception
     */
    private static function gatherData(string $url): array
    {
        $api_key = static::$apiKey;
        $headerData = "Authorization: Bearer $api_key";

        $apiToken = "";
        $tokens = [];

        do {
            if (!empty($apiToken)) {
                if (strpos($url, "&page_token=")) {
                    $url = strstr($url, "&page_token=", true);
                }
                $url = $url . "&page_token=$apiToken";
            }

            try {
                $ch = curl_init();
                if ($ch === false) {
                    throw new Exception('failed to initialize');
                }

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array($headerData));

                $json = curl_exec($ch);
                if ($json === false) {
                    throw new Exception(curl_error($ch), curl_errno($ch));
                }
                curl_close($ch);
            } catch (Exception $e) {
                throw new Exception($e);
            }

            $result = json_decode($json, 1);
            $data = $result["data"] ?? [];

            if (empty($result["meta"])) {
                $tokens["data"] = [];
            }

            foreach ($data as $tokenData) {
                $tokens["data"][] = $tokenData;
                $contract = $tokenData["contract_address"] ?? null;
                if (!is_null($contract) && !in_array($contract, self::$contractMap)) {
                    self::$contractMap[] = $contract;
                }
            }

            $apiToken = $result["meta"]["paging"]["next_page_token"] ?? "";
            sleep(0.1);
        } while (!empty($apiToken));

        return $tokens;
    }

    private static function simpleCall(string $url)
    {
        $api_key = static::$apiKey;
        $headerData = "Authorization: Bearer $api_key";

        try {
            $ch = curl_init();
            if ($ch === false) {
                throw new Exception('Failed to initialize');
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($headerData));

            $json = curl_exec($ch);
            if ($json === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            throw new Exception($e);
        }

        return json_decode($json, 1) ?? [];
    }
}
