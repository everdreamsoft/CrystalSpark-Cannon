<?php

namespace CsCannon\Blockchains\Ethereum\DataSource;


use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Ethereum\EthereumContract;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\SandraManager;
use Ethereum\DataType\Block;
use Ethereum\DataType\EthB;
use Ethereum\DataType\EthBlockParam;
use Ethereum\DataType\EthQ;
use Ethereum\DataType\FilterChange;
use Ethereum\Ethereum;
use Ethereum\SmartContract;
use Illuminate\Support\Facades\DB;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class BlockscoutAPI extends BlockchainDataSource
{

    public $sandra ;
    public static $chainUrl;
    public static $defaultChainUrl = 'https://blockscout.com/eth/mainnet/api';



    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        //temporary URL
        $foreignAdapter = new ForeignEntityAdapter(self::getChainUrl()
            ."?module=account&action=tokenlist&address=".$address->getAddress(),'result',SandraManager::getSandra());

        $assetVocabulary = array('image_url'=>'image',
            'assetName'=>'assetName',
            'name'=>'name',
        );

        $contractFactory = new EthereumContractFactory();
        $contractFactory->populateLocal();

        $foreignAdapter->populate();

        foreach ($foreignAdapter->getEntities() as $entity){

            $contract = $contractFactory->get($entity->get('contractAddress'));
            $standard = ERC20::init();

            //$standard->setTokenId("1");
            $address->balance->addContractToken($contract,$standard,$entity->get('balance'));


           // $address->balance->addContractToken($contract,new ERC721())
        }

        return $address->balance ;


    }

    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit, $offset): Balance
    {


        $foreignAdapter = new ForeignEntityAdapter(self::getChainUrl()
            ."?module=account&action=tokenlist&address=".$address->getAddress(),'result',SandraManager::getSandra());

        $assetVocabulary = array('image_url'=>'image',
            'assetName'=>'assetName',
            'name'=>'name',
        );

        $contractFactory = new EthereumContractFactory();
        $contractFactory->populateLocal();

        $foreignAdapter->populate();
        $contractAddressMap = [];

        foreach ($contract ?? [] as $contract){

            $contractAddressMap[$contract->getId()] = $contract; ;
        }



        foreach ($foreignAdapter->getEntities() as $entity){

            $contractAddress = $entity->get('contractAddress');
            $standard = ERC20::init();

            //we remove unwanted contracts
            if (!isset($contractAddressMap[$contractAddress])) continue ;
            $contract = $contractAddressMap[$contractAddress] ;

            if ($entity->get('contractAddress'))

            //$standard->setTokenId("1");
            $address->balance->addContractToken($contract,$standard,$entity->get('balance'));


            // $address->balance->addContractToken($contract,new ERC721())
        }

        return $address->balance ;


    }


    public static function getChainUrl(): String
    {

       if (!isset(self::$chainUrl)){

           self::$chainUrl = self::$defaultChainUrl ;

       }

       return self::$chainUrl;


    }


    public static function getEvents($contract = null, $batchMax = 1000, $offset = 0, $address = null): ForeignEntityAdapter
    {
        // TODO: Implement getEvents() method.
    }
}