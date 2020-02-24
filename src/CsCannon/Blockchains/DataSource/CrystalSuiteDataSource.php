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



        $foreignAdapter = new ForeignEntityAdapter(self::URL.'tokens/balances/'.$address->getAddress(),'data',SandraManager::getSandra());
       // $foreignAdapter->flatSubEntity('tokens','token');

        $foreignAdapter->populate();
        $me =  $foreignAdapter->divideForeignPath(['tokens'],'$first');


        //load all counterparty contracts onto memory
        $cpContracts = new XcpContractFactory();
        $cpContracts->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();
        $tokenStandardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
        $tokenStandardFactory->populateLocal();
        $xcpContractFactory = new XcpContractFactory();




        foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {

            /** @var ForeignEntity $rawContract */
            $contractR = $rawContract['contract'];
            $chain = $rawContract['chain'];
            $tokens = $rawContract['tokens'];

            $contract = $xcpContractFactory->get($contractR, true);


            if($chain != 'counterparty') continue ;
            foreach ($tokens ?? array() as $tokenData) {


                $balance->addContractToken($contract, $conterpartyAsset, $tokenData['quantity']);
            }

        }

        return $balance;



    }
}