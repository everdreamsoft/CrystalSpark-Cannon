<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Contracts\ERC20;
use CsCannon\Blockchains\Contracts\ERC721;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnContractFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\ForeignEntityAdapter;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 03.21.24
 * Time: 09:55
 */
class SandraApiDataSource extends BlockchainDataSource
{

    const URL = "http://172.105.244.176:3009/api";

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
        $url = self::URL . "/balance/" . $address->getAddress();
        $foreignAdapter = new ForeignEntityAdapter($url, "data", SandraManager::getSandra(), "", []);

        $data = $foreignAdapter->foreignRawArray["data"];

        $balance = new Balance();
        $balance->address = $address;
        foreach ($data as $blockchain => $addresses) {
            $contractFact = self::getContractFact($blockchain);
            foreach ($addresses as $address => $details) {
                $contract = $contractFact->get($address);
                foreach ($details['tokens'] as $token) {
                    $standard = self::getStandard(strtolower($details['standard']), $token['tokenId']);
                    $balance->addContractToken($contract, $standard, $token['quantity']);
                }
            }
        }

        return $balance;
    }


    private static function getContractFact($chain)
    {
        switch ($chain) {
            case KlaytnBlockchain::NAME:
                return new KlaytnContractFactory();
            case EthereumBlockchain::NAME:
                return new EthereumContractFactory();
            default:
                return new GenericContractFactory();
        }
    }

    private static function getStandard($stand, $token): \CsCannon\Blockchains\BlockchainContractStandard
    {

        if (strpos($stand, "erc721") !== false) {
            return ERC721::init($token);
        }

        if (strpos($stand, "erc20") !== false) {
            return ERC20::init();
        }

        return ERC20::init();
    }

}


