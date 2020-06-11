<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\BlockchainStandardFactory;
use CsCannon\SandraManager;
use SandraCore\DatabaseAdapter;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class XulNocDataSource extends BlockchainDataSource
{



    public const URL = 'https://xulnoc.everdreamsoft.com/api/v1/';







    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {



        $foreignAdapter = new ForeignEntityAdapter(static::URL.'tokens/balances/'.$address->getAddress(),'data',SandraManager::getSandra());
       // $foreignAdapter->flatSubEntity('tokens','token');

        $foreignAdapter->populate();
        $me =  $foreignAdapter->divideForeignPath(['tokens'],'$first');



        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();

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






            //if($chain != 'counterparty') continue ;
            foreach ($tokens ?? array() as $tokenData) {

                //WE should dynamically allocated correct token standard from name but lacking of time now
                if ('ERC721' == $tokenData['standard']){

                    $token =  ERC721::init($tokenData);
                    $balance->addContractToken($contract, $token, $tokenData['quantity']);
                }



            }

        }

        return $balance;



    }

    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit, $offset):Balance
    {

        return static::getBalance($address,$batchMax=1000,$offset=0);
    }

    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        // TODO: Implement getEvents() method.
    }
}