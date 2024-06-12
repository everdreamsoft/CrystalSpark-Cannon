<?php

namespace CsCannon\Blockchains\Polygon\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Polygon\PolygonContract;
use CsCannon\Blockchains\Polygon\PolygonContractFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\ForeignEntityAdapter;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 03.21.24
 * Time: 09:55
 */
class AlchemyDataSource extends BlockchainDataSource
{
    const NETWORK_ETH_MAIN = "eth-mainnet";

    const NETWORK_MAINNET = "polygon-mainnet";
    const NETWORK_MUMBAI = "polygon-mumbai";

    public static $apiKey = '2U3ERhEqMX2qjwpNjadYJTCCDWgQRCiK';
    public static $network = "polygon-mumbai";

    /**
     * @param string $apiKey
     */
    public static function setApiKey(string $apiKey): void
    {
        self::$apiKey = $apiKey;
    }

    /**
     * @throws Exception
     */
    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        throw new Exception("Not implemented");
    }

    /**
     * @throws Exception
     */
    public static function getBalance(BlockchainAddress $address, $limit, $offset, string $network = AlchemyDataSource::NETWORK_MUMBAI): Balance
    {
        return self::getBalanceForContract($address, array(), $limit, null, $network);
    }

    /**
     * @throws Exception
     */
    public static function getBalanceForContract(BlockchainAddress $address, array $contracts, $pageSize = 100, $pageKey = null, string $network = AlchemyDataSource::NETWORK_MUMBAI): Balance
    {
        $contractList = "";
        foreach ($contracts as $contract) {
            $contractList = $contractList . "&contractAddresses[]=" . $contract;
        }
        $headers = "'accept': 'application/json'";

        $url = "https://" . $network
            . ".g.alchemy.com/nft/v2/" . AlchemyDataSource::$apiKey
            . "/getNFTs?"
            . "owner=" . $address->getAddress()
            . "&withMetadata=false&orderBy=transferTime"
            . "&pageSize=" . $pageSize
            . $contractList;

        $foreignArray = [];

        do {
            $pagedUrl = $url . (($pageKey) ? "&pageKey=" . $pageKey : "");
            $adapter = new  ForeignEntityAdapter($pagedUrl, "ownedNfts", SandraManager::getSandra(), $headers);
            $pageKey = $adapter->foreignRawArray["pageKey"] ?? null;

            if (isset($adapter->foreignRawArray["ownedNfts"]))
                $foreignArray = array_merge($foreignArray, $adapter->foreignRawArray["ownedNfts"]);

        } while ($pageKey != null);


        $foreignAdapter = new ForeignEntityAdapter("", null, SandraManager::getSandra(), "", $foreignArray);
        $foreignAdapter->flatSubEntity('contract', 'contract');
        $foreignAdapter->flatSubEntity('id', 'id');
        $foreignAdapter->adaptToLocalVocabulary(array(
            'contract.address' => 'contract',
            'id.tokenId' => "tokenId",
            'balance' => "quantity",
        ));
        $foreignAdapter->populate();
        $tokensEntities = $foreignAdapter->getEntities();

        $balance = new Balance();
        $balance->address = $address;

        $contractsFound = [];
        foreach ($tokensEntities as $token) {
            $contractAddress = $token->get('contract');
            if (!is_null($contractAddress) && !in_array($contractAddress, $contractsFound)) {
                $contractsFound[] = $contractAddress;
            }
        }

        if (count($contractsFound) == 0) {
            return $balance;
        }

        $contractFactory = new PolygonContractFactory();
        $contractFactory->populateFromSearchResults($contractsFound, BlockchainContractFactory::MAIN_IDENTIFIER);

        foreach ($tokensEntities as $token) {
            $contractFactory = new PolygonContractFactory();
            $tokenContractAddress = $token->get("contract");

            /** @var PolygonContract $contract */
            $contract = $contractFactory->get($tokenContractAddress);
            if ($contract) {
                $hexTokenId = $token->get('tokenId');
                $quantity = $token->get('quantity');
                $cleanHexString = substr($hexTokenId, 2);

                try {
                    $tokenId = hexdec($cleanHexString);
                } catch (Exception $exception) {
                    $tokenId = $cleanHexString;
                }

                $standard = ERC721::init($tokenId);
                $balance->addContractToken($contract, $standard, $quantity);
            }
        }

        return $balance;

    }

    public static function getTokenIdFromTx(string $blockchainName, string $txHash, string $network = AlchemyDataSource::NETWORK_MUMBAI): ?array
    {
        $data = ["status" => "pending"];
        $receipt = AlchemyDataSource::getTransactionReceipt($txHash, $network);

        if ($receipt === null) {
            return $data;
        }

        if (isset($receipt['status'])) {
            if ($receipt['status'] === '0x1') {
                $data['status'] = 'completed';
            } else {
                $data['status'] = 'failed';
                return $data;
            }
        }

        if (isset($receipt["logs"])) {
            foreach ($receipt["logs"] as $log) {
                try {
                    $decodedLog = AlchemyDataSource::decodeTransferLog($log["topics"]);
                    if ($decodedLog) {
                        $data["transfers"][] = $decodedLog;
                    }
                } catch (Exception $e) {
                }
            }
        }

        return $data;
    }

    public static function getTransactionStatus(string $blockchainName, string $txHash, string $network = AlchemyDataSource::NETWORK_MUMBAI): ?string
    {

        $receipt = AlchemyDataSource::getTransactionReceipt($txHash, $network);

        if ($receipt === null) {
            return null;
        }

        if (isset($receipt['status'])) {
            if ($receipt['status'] === '0x1') {
                return "true";
            } else {
                return "false";
            }
        } else {
            return null;
        }
    }

    /**
     * @throws Exception
     */
    public static function getTransactionDetails(string $blockchainName, string $txHash, string $network): ?stdClass
    {
        throw new Exception("Not implemented");
    }

    public static function getERC20Tokens(string $address, string $network)
    {

        $url = "https://" . $network
            . ".g.alchemy.com/v2/" . AlchemyDataSource::$apiKey;

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        $data = [
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => "alchemy_getTokenBalances",
            "params" => [
                $address,
                "erc20"
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        if (isset($responseData['error'])) {
            return null;
        }

        if (isset($responseData['result'])) {
            return $responseData['result'];
        }

        return null;

    }

    public static function getERC20ContractMetaData(string $contract, string $network)
    {

        $url = "https://" . $network
            . ".g.alchemy.com/v2/" . AlchemyDataSource::$apiKey;

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        $data = [
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => "alchemy_getTokenMetadata",
            "params" => [
                $contract
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        if (isset($responseData['error'])) {
            return null;
        }

        if (isset($responseData['result'])) {
            return $responseData['result'];
        }

        return null;

    }


    private static function getTransactionReceipt(string $txHash, string $network): ?array
    {

        $url = "https://" . $network
            . ".g.alchemy.com/v2/" . AlchemyDataSource::$apiKey;

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        $data = [
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionReceipt",
            "params" => [
                $txHash
            ]
        ];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        $responseData = json_decode($response, true);

        if (isset($responseData['error'])) {
            return null;
        }

        if (isset($responseData['result'])) {
            return $responseData['result'];
        }

        return null;

    }

    private static function decodeTransferLog($topics): ?array
    {

        if ($topics == null) return null;

        // Transfer event signature, we should use ABI here, but how do you get the ABI?? and how to decode, current ethereum plugins for php are
        // compatible with php 7?
        $transferEventSig = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

        if (strtolower($topics[0]) !== $transferEventSig) {
            return null;
        }

        $from = '0x' . substr($topics[1], 26);
        $to = '0x' . substr($topics[2], 26);
        $tokenId = hexdec(substr($topics[3], 26));

        return [
            'from' => $from,
            'to' => $to,
            'tokenId' => $tokenId
        ];
    }

}


