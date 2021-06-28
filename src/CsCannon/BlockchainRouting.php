<?php

namespace CsCannon;




use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;

use CsCannon\Blockchains\Ethereum\GoerliEthereumBlockchain;
use CsCannon\Blockchains\Ethereum\RopstenEthereumBlockchain;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain;
use CsCannon\Blockchains\FirstOasis\FirstOasisBlockchain;
use CsCannon\Blockchains\Generic\GenericAddress;
use CsCannon\Blockchains\Generic\GenericAddressFactory;
use CsCannon\Blockchains\Generic\GenericContract;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use CsCannon\Blockchains\Substrate\Kusama\WestendBlockchain;
use CsCannon\Blockchains\Substrate\Unique\UniqueBlockchain;
use SandraCore\Concept;
use SandraCore\DatabaseAdapter;
use SandraCore\Entity;
use SandraCore\System;

class BlockchainRouting
{


    /**
     * @var Blockchain[]
     */
    public static $hotPluggedBlockchain = [] ;
    public static $supportedChains = [] ;

    /**
     * get supported blockchains by the framework
     * @return Blockchain[]
     */
    public static function getSupportedBlockchains()
    {

        $supported[] = new XcpBlockchain();
        $supported[] = new EthereumBlockchain();
        $supported[] = new MaticBlockchain();
        $supported[] = new KlaytnBlockchain();
        $supported[] = new GoerliEthereumBlockchain();
        $supported[] = new UniqueBlockchain();
        $supported[] = new KusamaBlockchain();
        $supported[] = new WestendBlockchain();


        $supported = array_merge($supported,self::$hotPluggedBlockchain);

        return $supported;

    }

    public static function addBlockchainSupport(Blockchain $blockchain)
    {
        $hotPlugged = self::$hotPluggedBlockchain;

        if (self::getBlockchainFromName($blockchain::NAME) == null) {

            $hotPlugged[] = $blockchain;
            self::$hotPluggedBlockchain = $hotPlugged;
        }

        return $blockchain ;

    }


    public static function getBlockchainFromName($name): ?Blockchain
    {

        $supported = self::getSupportedBlockchains();

        foreach ($supported as $blockchain) {

            if ($name == $blockchain::NAME) return $blockchain;

        }
        return null;

    }

    /**
     * Deduct potential blockchains from an address
     *
     * @param $address
     * @return Blockchain[]
     */
    public static function getBlockchainsFromAddress($address)
    {

        //as for today 0x means all ethereum type format
        if (substr($address, 0, 2) === "0x") {

            $blockchainList['eth'] = $address;
            $blockchains[] = new EthereumBlockchain();
            $blockchains[] = new KlaytnBlockchain();
            $blockchains[] = new \CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain();


        } else {

            $blockchainList['xcp'] = $address;
            $blockchains[] = new XcpBlockchain();

        }

        return $blockchains;


    }

    /**
     * Get Address factories from an array of blockchains
     *
     * @param Blockchain[] $blockchainArray
     * @return BlockchainAddressFactory[]
     */
    public static function getAddressFactoriesFromBlockchains(array $blockchainArray)
    {

        $addressFactories = array();

        foreach ($blockchainArray as $blockchain) {

            $addressFactories[$blockchain::NAME] = $blockchain->getAddressFactory();
        }

        return $addressFactories;

    }

    /**
     * Deduct potential blockchains from an address and return factories
     *
     * @param  $address
     * @return BlockchainAddressFactory[]
     */
    public static function getAddressFactoriesFromAddress($address)
    {

        $blockchains = self::getBlockchainsFromAddress($address);
        $addressFactories = self::getAddressFactoriesFromBlockchains($blockchains);

        return $addressFactories;

    }


    public static function blockchainFromAddress($address)
    {

        //as Force klaytn
        if (substr($address, 0, 2) === "0x") {

            $blockchainList['klay'] = $address;
            $blockchain = new KlaytnBlockchain();


        }

        //as for today 0x means ethereum
        if (substr($address, 0, 2) === "0x") {

            $blockchainList['eth'] = $address;
            $blockchain = new EthereumBlockchain();


        }  else if (substr($address, 0, 3) === "@f:") {

            $blockchainList['fo'] = $address;
            $blockchain = new FirstOasisBlockchain();


        } else {

            $blockchainList['xcp'] = $address;
            $blockchain = new XcpBlockchain();

        }

        return $blockchain;


    }

    public static function getAddressFactory($deducable): BlockchainAddressFactory
    {


        $blockchain = self::blockchainFromAddress($deducable);


        return $blockchain->getAddressFactory();


    }

    public static function getAddressFactories($address): BlockchainAddressFactory
    {


        $blockchain = self::blockchainFromAddress($address);


        return $blockchain->getAddressFactory();


    }

    public static function getContractFactory($deducable)
    {


        $blockchain = self::blockchainFromAddress($deducable);

        return $blockchain->getContractFactory();


    }

    public static function getEventFactory($deducable): BlockchainEventFactory
    {


        $blockchain = self::blockchainFromAddress($deducable);

        return $blockchain->getEventFactory();


    }

    /**
     *
     * A generic contract is expected but any Blockchain contract is accepted for function stability purpose.
     * If the Generic contract is defined as specific chain contract return the specific chain
     *
     * @param BlockchainContract $contract
     * @return Blockchain[]
     */
    public static function getBlockchainFromGenericContract(BlockchainContract $contract): array
    {

        if (!$contract instanceof GenericContract) return [$contract->getBlockchain()];

        $arrayOfBlockchains = $contract->getBrotherEntity(BlockchainContractFactory::ON_BLOCKCHAIN_VERB);
        $arrayOfChains = [];

        foreach ($arrayOfBlockchains ?? array() as $onBlockchainEntity) {
            /** @var Entity $onBlockchainEntity */
            $conceptTarget = $onBlockchainEntity->targetConcept;
            $conceptShortname = $conceptTarget->getShortname();
            $blockchain = self::getBlockchainFromName($conceptShortname);
            $arrayOfChains[] = $blockchain;


        }

        return $arrayOfChains;


    }

    /**
     *
     * A generic address is expected but any Blockchain contract is accepted for function stability purpose.
     * If the Generic address is defined as specific chain address return the specific chain
     *
     * @param BlockchainAddress $contract
     * @return Blockchain[]
     */
    public static function getBlockchainFromGenericAddress(BlockchainAddress $address): array
    {

        if (!$address instanceof GenericAddress) return [$address->getBlockchain()];

        $arrayOfBlockchains = $address->getBrotherEntity(BlockchainContractFactory::ON_BLOCKCHAIN_VERB);
        $arrayOfChains = [];

        foreach ($arrayOfBlockchains ?? array() as $onBlockchainEntity) {
            /** @var Entity $onBlockchainEntity */
            $conceptTarget = $onBlockchainEntity->targetConcept;
            $conceptShortname = $conceptTarget->getShortname();
            $blockchain = self::getBlockchainFromName($conceptShortname);
            $arrayOfChains[] = $blockchain;

        }

        //if data was build on legacy version we don't have onBlockchain entity. We we get with the is_a array
        if (empty($arrayOfChains)) {
            $systemConcept = $address->system->systemConcept;

            $address->factory->getTriplets();


            //address has no defined blockchain
            if (!isset($address->subjectConcept->tripletArray[$systemConcept->get('is_a')])){

               return  self::getBlockchainsFromAddress($address->getAddress());
            }

            $arrayOfBlockchainsIs_a__address = $address->subjectConcept->tripletArray[$systemConcept->get('is_a')];


            foreach ($arrayOfBlockchainsIs_a__address ?? array() as $is___AddressUnid) {

                $name = $systemConcept->getSCS($is___AddressUnid);

                switch ($name) {

                    case "ethAddress":
                        $arrayOfChains[] = EthereumBlockchain::getStatic();
                        break;
                    case "btcAddress":
                        $arrayOfChains[] = XcpBlockchain::getStatic();
                        break;
                    case "klaytnAddress":
                        $arrayOfChains[] = KlaytnBlockchain::getStatic();
                        break;

                }

            }

        }


        return $arrayOfChains;

    }


    public static function getDataPath($blockchain, $type)
    {

        switch ($blockchain) {

            case 'xcp' :
                $path['eventFile'] = 'btcBlockchainEventFile';
                $path['eventIs_a'] = 'btcBlockchainEvent';
                $path['addressFile'] = 'btcAddressFile';
                $path['addressIs_a'] = 'btcAddress';

                break;


        }

        if (isset($path[$type]))
            return $path[$type];

        return null;

    }


    public static function searchConceptFromString($string,$sandra)
    {

        $return = array();

        $concepts = DatabaseAdapter::searchConcept($sandra,$string, null);

        foreach ($concepts ?? array() as $conceptId) {

            $concept = new Concept($conceptId, $sandra);
            $triplets = $concept->getConceptTriplets();
            $return [] = self::getEntityFromTriplets($concept,$triplets, $sandra);

        }

        return $return ;

    }

    private static function getEntityFromTriplets(Concept $concept,$array,System $sandra)
    {

       $sc =  $sandra->systemConcept;

        $return = null ;

        foreach ($array[$sc->get('contained_in_file')] ?? array() as $target){

                //is it contract ?
                    if ($target == $sc->get(BlockchainContractFactory::$file)) {

                        $genericFactory = new GenericContractFactory();
                        $genericFactory->conceptArray = [$concept->idConcept];
                        $genericFactory->populateLocal();
                    $allEntitties = $genericFactory->getEntities();
                        $genericEntity = end($allEntitties);
                        $blockchains = self::getBlockchainFromGenericContract($genericEntity);
                    if (!isset($blockchains[0])) continue ;
                        $blockchain = $blockchains[0];
                        $correctChainContract = $blockchain->getContractFactory()->get($genericEntity->getId());
                    $return = $correctChainContract ;
                }

            //is it contract ?
            if ($target == $sc->get(BlockchainAddressFactory::$file)) {

                $genericFactory = new GenericAddressFactory();
                $genericFactory->conceptArray = [$concept->idConcept];
                $genericFactory->populateLocal();
                $allEntitties = $genericFactory->getEntities();
                $genericEntity = end($allEntitties);
                $blockchains = self::getBlockchainFromGenericAddress($genericEntity);
                if (!isset($blockchains[0])) continue ;
                $blockchain = $blockchains[0];
                $correctChainContract = $blockchain->getAddressFactory()->get($genericEntity->getAddress());
                $return = $correctChainContract ;
            }



        }

        return $return ;



    }

}
