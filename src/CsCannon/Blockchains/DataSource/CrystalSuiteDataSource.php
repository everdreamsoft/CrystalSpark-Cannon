<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
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
class CrystalSuiteDataSource extends BlockchainDataSource
{



    public const URL = 'https://baster.bitcrystals.com/api/v1/';




    public static function getEvents($contract=null,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter
    {

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$address->getAddress(),'data',
            SandraManager::getSandra());

        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$address->getAddress(),'data',SandraManager::getSandra());

        //$foreignEntityAdapter->addNewEtities($entityArray,array());

        return $foreignAdapter ;
    }


    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {



        $foreignAdapter = new ForeignEntityAdapter(self::URL.'/tokens/balance/'.$address->getAddress(),'data',SandraManager::getSandra());

        $foreignAdapter->populate();

        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();

        foreach ($foreignAdapter->entityArray as $entity) {

            /** @var ForeignEntity $entity */
            $contract = $cpContracts->get($entity->get('contractId'),true);


            $balance->addContractToken($contract,$conterpartyAsset,$entity->get('balance'));

        }

        return $balance;



    }
}