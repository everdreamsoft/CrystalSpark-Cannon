<?php

namespace CsCannon\Blockchains\Counterparty\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 12.11.24
 * Time: 09:55
 */
class OrbitalApiDataSource extends BlockchainDataSource
{

    private static string $url = "https://api-develop.counterparty.market";


    public static function setApiUrl($url)
    {
        OrbitalApiDataSource::$url = $url;
    }

    /**
     * @throws Exception
     */
    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        throw new Exception("Not implemented");
    }

    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        $url = self::$url . "/balances?addresses[]=" . $address->getAddress() . "&withFullData=true&" . "\$limit=" . ($limit ?? 1000);
        $foreignAdapter = new ForeignEntityAdapter($url, "data", SandraManager::getSandra(), "", []);
        $foreignAdapter->populate();

        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $asset = CounterpartyAsset::init();

        foreach ($foreignAdapter->entityArray as $entity) {
            /** @var ForeignEntity $entity */
            $contract = $cpContracts->get($entity->get('asset'), true);
            $contract->setXcpPrice($entity->get('xcp_price') ?? 0);
            $contract->setBtcPrice($entity->get('btc_price') ?? 0);
            $balanceValue = $entity->get('quantity') ?? 0;
            if ($entity->get('divisible')) $contract->setDivisible();
            $balance->addContractToken($contract, $asset, $balanceValue);
        }

        return $balance;

    }

    public static function getOrbsByCollection(string $address, $limit = 1000): array
    {

        $url = self::$url . "/balances?addresses[]=" . $address . "&withFullData=true&" . "\$limit=" . ($limit ?? 1000);
        $orbsAdapter = new ForeignEntityAdapter($url, "data", SandraManager::getSandra(), "", []);
        $orbsAdapter->populate();

        $collections = [];

        foreach ($orbsAdapter->entityArray as $entity) {

            $collectionId = $entity->get("collection_slug") ?? "XCP";

            if (!isset($collections[$collectionId])) {
                $collections[$collectionId] = [
                    "id" => $collectionId,
                    "name" => $entity->get("collection_name") ?? "Counterparty",
                    "imageUrl" => $entity->get("collection_logo") ?? "",
                    "bannerImage" => $entity->get("collection_logo") ?? "",
                    "description" => $entity->get("collection_description") ?? "",
                    "orbs" => []
                ];
            }

            $collections[$collectionId]["orbs"][] = [
                "contract" => $entity->get("asset"),
                "chain" => "counterparty",
                "token" => ["standard" => "Counterparty Token"],
                "adaptedQuantity" => null, // Support already implemented logic for casa tookan server, quantity will be taken as adaptive quantity.
                "quantity" => $entity->get("quantity") ?? 0,
                "btcPrice" => $entity->get("btc_price") ?? 0,
                "xcpPrice" => $entity->get("xcp_price") ?? 0,
                "asset" => [
                    "image" => $entity->get("orb_image"),
                    "id" => $entity->get("asset"),
                    "name" => $entity->get("name") ?? $entity->get("asset"),
                    "description" => ""
                ],
            ];

        }

        return $collections;
    }

}


