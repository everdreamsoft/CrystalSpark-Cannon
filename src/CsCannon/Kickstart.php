<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-07-08
 * Time: 11:27
 */

namespace CsCannon;

class Kickstart
{

    public static function createViews(){

        $ethContractFactory = new \CsCannon\Blockchains\Ethereum\EthereumContractFactory();
        $ethContractFactory->populateLocal();
        $ethContractFactory->createViewTable("EthereumContracts");

        $factory = new \CsCannon\Blockchains\Ethereum\EthereumAddressFactory();
        $factory->populateLocal();
        $factory->createViewTable("EthereumAddress");

        $factory = new \CsCannon\AssetCollectionFactory();
        $factory->populateLocal();
        $factory->createViewTable("AssetCollections");

        $factory = new \CsCannon\AssetFactory();
        $factory->populateLocal();
        $factory->createViewTable("Assets");





    }

}