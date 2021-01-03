<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-07-08
 * Time: 11:27
 */

namespace CsCannon;

use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticContractFactory;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticEventFactory;
use CsCannon\Blockchains\Klaytn\KlaytnAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnContractFactory;
use SandraCore\EntityFactory;
use SandraCore\System;

class Kickstart
{

    public static function createViews(){

        $sandra = SandraManager::getSandra();

        self::createViewFromFactory(new EthereumContractFactory(),'EthereumContracts');
        self::createViewFromFactory(new EthereumAddressFactory(),'EthereumAddress');
        self::createViewFromFactory(new KlaytnContractFactory(),'KlatnContracts');
        self::createViewFromFactory(new KlaytnAddressFactory(),'KlaytnAddress');
        self::createViewFromFactory(new MaticContractFactory(),'MaticContracts');
        self::createViewFromFactory(new MaticEventFactory(),'MaticEvents');

        self::createViewFromFactory(new EntityFactory("balanceItem",'balanceFile',$sandra),'balances');
        self::createViewFromFactory(new BlockchainEventFactory(),'Events');
        self::createViewFromFactory(new \CsCannon\AssetCollectionFactory($sandra),'AssetCollections');
        self::createViewFromFactory(new \CsCannon\AssetFactory(),'Assets');

        self::createViewFromFactory(new XcpContractFactory(),'XcpContracts');


    }

    public static function createViewFromFactory(EntityFactory $factory, $viewName){


        $factory->populateLocal();
        if (count($factory->getEntities())>0){
            $factory->createViewTable("$viewName");
        }




    }

}