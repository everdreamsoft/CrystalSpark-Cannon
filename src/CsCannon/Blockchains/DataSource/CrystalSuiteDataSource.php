<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
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



        $foreignAdapter = new ForeignEntityAdapter(static::URL . 'tokens/balances/' . $address->getAddress(), 'data', SandraManager::getSandra());
        // $foreignAdapter->flatSubEntity('tokens','token');

        $foreignAdapter->populate();
        $me = $foreignAdapter->divideForeignPath(['tokens'], '$first');


        //load all counterparty contracts onto memory
        $genericContractFactory = new GenericContractFactory();
        $genericContractFactory->populateLocal();

        $balance = new Balance($address);
        $conterpartyAsset = CounterpartyAsset::init();
        $tokenStandardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
        $tokenStandardFactory->populateLocal();

        $contractIdMap =[];
        $contractFactoryMap = [] ;

        //we build a contract map
        foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {

            $chain = $rawContract['chain'];
            $contractIdMap[$chain][] = $contractId = $rawContract['contract'];

            if (!isset($contractFactoryMap[$chain]))
                $contractFactoryMap[$chain] = BlockchainRouting::getBlockchainFromName($chain)->getContractFactory();

        }

        static::populateContractFactory($contractIdMap,$contractFactoryMap);



        foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {

            /** @var ForeignEntity $rawContract */
            $contractR = $rawContract['contract'];
            $chain = $rawContract['chain'];
            $tokens = $rawContract['tokens'];


            $blockchainStandardFactory = new BlockchainStandardFactory($foreignAdapter->system);
            $blockchainStandardFactory->populateLocal();

            $contract = $contractFactoryMap[$chain]->get($contractR);



            foreach ($tokens ?? array() as $tokenData) {


                $standard = UnknownStandard::init();

                //this is very bad
                switch ($tokenData['standard']){
                    case "ERC721":
                        $standard = ERC721::init($tokenData['tokenId']);
                        break ;
                    case "Counterparty Token":
                        $standard = CounterpartyAsset::init();
                        break ;
                    case "ERC20":
                        $standard = ERC20::init();
                        break ;

                }


                $balance->addContractToken($contract, $standard, $tokenData['quantity']);
            }

        }

        return $balance;


    }

    /**
     * @param BlockchainAddress $address
     * @param BlockchainContract[] $contractArray
     * @param $limit
     * @param $offset
     * @return Balance
     * @throws \Exception
     */
    public static  function getBalanceForContract(BlockchainAddress $address, array $contractArray, $limit, $offset):Balance
    {
        $keepIdList = array();
            foreach( $contractArray as $contract){

                $keepIdList[]  = $contract->getId();

            }


            //THIS IS REPETED IT SHOULD NOT !!
            $foreignAdapter = new ForeignEntityAdapter(static::URL . 'tokens/balances/' . $address->getAddress(), 'data', SandraManager::getSandra());
            // $foreignAdapter->flatSubEntity('tokens','token');

            $foreignAdapter->populate();
            $me = $foreignAdapter->divideForeignPath(['tokens'], '$first');


            //load all counterparty contracts onto memory
            $genericContractFactory = new GenericContractFactory();
            $genericContractFactory->populateLocal();

            $balance = new Balance($address);
            $conterpartyAsset = CounterpartyAsset::init();
            $tokenStandardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
            $tokenStandardFactory->populateLocal();

            $contractIdMap =[];
            $contractFactoryMap = [] ;

            //we build a contract map
           foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {

               $chain = $rawContract['chain'];
               $contractIdMap[$chain][] = $contractId = $rawContract['contract'];

               if (!isset($contractFactoryMap[$chain]))
               $contractFactoryMap[$chain] = BlockchainRouting::getBlockchainFromName($chain)->getContractFactory();

            }

           static::populateContractFactory($contractIdMap,$contractFactoryMap);



            foreach ($foreignAdapter->foreignRawArray['data'] ?? array() as $rawContract) {

                /** @var ForeignEntity $rawContract */
                $contractR = $rawContract['contract'];
                $chain = $rawContract['chain'];
                $tokens = $rawContract['tokens'];


                $blockchainStandardFactory = new BlockchainStandardFactory($foreignAdapter->system);
                $blockchainStandardFactory->populateLocal();

                $contract = $contractFactoryMap[$chain]->get($contractR);

                if (!in_array($contractR,$keepIdList)){

                    continue ;
                }



                foreach ($tokens ?? array() as $tokenData) {


                    $standard = UnknownStandard::init();

                    //this is very bad
                    switch ($tokenData['standard']){
                        case "ERC721":
                            $standard = ERC721::init($tokenData['tokenId']);
                            break ;
                        case "Counterparty Token":
                            $standard = CounterpartyAsset::init();
                            break ;
                        case "ERC20":
                            $standard = ERC20::init();
                            break ;

                    }


                    $balance->addContractToken($contract, $standard, $tokenData['quantity']);
                }

            }

            return $balance;



    }

    /**
     * @param array[] $contractIdMap
     * @param BlockchainContractFactory[] $contractFactoryMap
     */
    private static function populateContractFactory($contractIdMap, $contractFactoryMap){

        foreach ($contractFactoryMap as $chain =>$factory){

            $factory->populateFromSearchResults( $contractIdMap[$chain],BlockchainContractFactory::MAIN_IDENTIFIER);

        }






    }
}