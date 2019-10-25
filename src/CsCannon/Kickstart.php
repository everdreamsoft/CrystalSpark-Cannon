<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-07-08
 * Time: 11:27
 */

namespace CsCannon;

use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnContractFactory;
use SandraCore\System;

class Kickstart
{

    public static function createViews(){

        $sandra = SandraManager::getSandra();

        $ethContractFactory = new EthereumContractFactory();
        $ethContractFactory->populateLocal();
        $ethContractFactory->createViewTable("EthereumContracts");

        $factory = new EthereumAddressFactory();
        $factory->populateLocal();
        $factory->createViewTable("EthereumAddress");

        $ethContractFactory = new KlaytnContractFactory();
        $ethContractFactory->populateLocal();
        $ethContractFactory->createViewTable("KlatnContracts");

        $factory = new KlaytnAddressFactory();
        $factory->populateLocal();
        $factory->createViewTable("KlaytnAddress");

        $factory = new BlockchainEventFactory();
        $factory->populateLocal();
        $factory->createViewTable("Events");

        $factory = new \CsCannon\AssetCollectionFactory($sandra);
        $factory->populateLocal();
        $factory->createViewTable("AssetCollections");

        $factory = new \CsCannon\AssetFactory();
        $factory->populateLocal();
        $factory->createViewTable("Assets");





    }

}