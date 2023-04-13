<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Contracts\ERC1155;
use CsCannon\Blockchains\Contracts\ERC721;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\BlockchainStandardFactory;
use CsCannon\SandraManager;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 04.03.2023
 * Time: 10:30
 */
class BalanceAPISource extends BlockchainDataSource
{
    // TODO - Change URL to production
    public const URL = 'http://139.162.176.241:9010/';

    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        $foreignAdapter = new ForeignEntityAdapter(static::URL . 'graph/balances/' . $address->getAddress(), 'data', SandraManager::getSandra());
        $foreignAdapter->populate();
        $balance = new Balance($address);

        $contractFactory = new GenericContractFactory();
        $contractFactory->populateLocal();

        $standardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
        $standardFactory->populateLocal();

        foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {
            /** @var ForeignEntity $rawContract */
            $contractR = $rawContract['contract'];
            $chain = $rawContract['chain'];
            $tokens = $rawContract['tokens'];
            $contract = $contractFactory->get($contractR, true);
            foreach ($tokens ?? array() as $tokenData) {
                // TODO - Standard Classes
                if ('ERC721' == $tokenData['standard']) {
                    $token = ERC721::init($tokenData);
                } else if ('ERC1155' == $tokenData['standard']) {
                    $token = ERC1155::init($tokenData);
                }

                $balance->addContractToken($contract, $token, $tokenData['quantity']);
            }
        }
        return $balance;
    }

    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit, $offset): Balance
    {
        return static::getBalance($address, $batchMax = 1000, $offset = 0);
    }

    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        // TODO: Implement getEvents() method.
    }

}
