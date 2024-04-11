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

    const NETWORK_MAINNET = "polygon-mainnet";
    const NETWORK_MUMBAI = "polygon-mumbai";

    public static $apiKey = '2U3ERhEqMX2qjwpNjadYJTCCDWgQRCiK';
    public static $network = "polygon-mumbai";

    public function __construct(string $network)
    {
        AlchemyDataSource::$network = $network ?? self::NETWORK_MUMBAI;
    }

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
    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {
        return self::getBalanceForContract($address, array(), $limit);
    }

    /**
     * @throws Exception
     */
    public static function getBalanceForContract(BlockchainAddress $address, array $contracts, $pageSize = 100, $pageKey = null): Balance
    {
        $contractList = "";
        foreach ($contracts as $contract) {
            $contractList = $contractList . "&contractAddresses[]=" . $contract;
        }
        $headers = "'accept': 'application/json'";

        $url = "https://" . AlchemyDataSource::$network
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


    /**
     * @throws Exception
     */
    public static function getTransactionDetails(string $txHash): array
    {
        throw new Exception("Not implemented");

    }

}


