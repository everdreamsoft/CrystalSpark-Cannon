<?php

namespace CsCannon\Blockchains\Ethereum\DataSource;


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
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Ali Anwar
 * Date: 28.10.22
 * Time: 14:00
 */
class BlockDaemonDataSource extends BlockchainDataSource
{

    public $sandra;
    public static $apiUrl = 'https://svc.blockdaemon.com/nft/v1/ethereum/mainnet';
    public static $apiKey = 'WHdH48fcHG94ZjKdOhOGUsDdPI1gCSdzqeBINxA8M9RQXzuz';
    // public static $apiKey = '39HnB9255yMUYt86LLuyqXbEnJKPMuWdHBiQSsx9zNnj2jTG';

    private static array $contractMap = [];


    // TODO find out how to get events from BlockDaemon and where do we use it
    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        // Probably not necessary anymore

        if (empty($batchMax) || $batchMax > 100) {
            $batchMax = 100;
        }

        $address = self::getAddressString($address);
        $addressFilter = "";
        if (!is_null($address)) {
            $addressFilter = "&wallet_address=$address";
        }

        $contractFilter = "";
        if (!is_null($contract)) {
            $contractFilter = "&contract_address=$contract";
        }

        $sandra =  SandraManager::getSandra();
        /** @var System $sandra */
        //        $openSeaEvents =  static::$apiUrl."events/?event_type=transfer&limit=$batchMax&offset=$offset".$addressFilter;
        $blockDaemonEvents =  static::$apiUrl . "events?event_type=transfer&page_size=$batchMax" . $addressFilter . $contractFilter;

        $result = self::gatherData(static::$apiUrl . "/events?event_type=transfer&page_size=$batchMax" . $addressFilter . $contractFilter);

        $formattedForeign = new ForeignEntityAdapter(null, null, SandraManager::getSandra());

        $blockDaemonVocabulary = [
            'id' => 'id',
            'timestamp' => BlockchainEventFactory::EVENT_BLOCK_TIME,
            'from_account' => BlockchainEventFactory::EVENT_SOURCE_ADDRESS,
            'to_account' => BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB,
            ''
        ];


        return $formattedForeign;
    }

    /**
     * @throws Exception
     */
    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {
        return self::getBalanceForContract($address, array(), $limit, $offset);
    }

    /**
     * @param BlockchainAddress $address
     * @param array $contract
     * @param $limit
     * @param $offset
     * @return Balance
     * @throws Exception
     */
    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit, $offset): Balance
    {
        if ($limit > 100) $limit  = 100;

        $wallet_address = $address->getAddress();
        $tokens = [];

        if (!empty($contracts)) {
            foreach ($contracts as $contract) {
                $data = self::gatherData(static::$apiUrl . "/assets?wallet_address=$wallet_address&page_size=$limit?contract_address=" . $contract->getId());
                $tokens = array_merge($tokens, $data);
            }
        }

        // Because of max limit = 100 / call on this api, have to call several times
        $tokens = self::gatherData(static::$apiUrl . "/assets?wallet_address=$wallet_address&page_size=$limit");

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

            $contractStandard =  UnknownStandard::init();
            $standard =  ERC721::init();
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

    public static  function  setApiKey($key)
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

            if (is_null($result["meta"])) {
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
        } while (!empty($token));

        return $tokens;
    }
}
