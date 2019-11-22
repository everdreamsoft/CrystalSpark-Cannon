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




    public static function getEvents($contract,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter{


        if (!is_null($address)) $addressFilter = "&account_address=$address";

        $sandra =  SandraManager::getSandra();
        /** @var System $sandra */
        $openSeaEvents = 'https://api.opensea.io/api/v1/events/?event_type=transfer'.$addressFilter;



        $formattedForeign = new ForeignEntityAdapter(null,null,SandraManager::getSandra());



        $openSeaVocabulary = array(
            'id' => Blockchain::$provider_opensea_enventId,
            'transaction.timestamp' => 'timestamp',
            'transaction.from_account' => BlockchainEventFactory::EVENT_SOURCE_ADDRESS,
            'transaction.to_account' => BlockchainEventFactory::EVENT_DESTINATION_VERB,
            'transaction.transaction_hash' => Blockchain::$txidConceptName,



        );






        $foreignEntityAdapter = new ForeignEntityAdapter($openSeaEvents,'asset_events',SandraManager::getSandra());
        $foreignEntityAdapter->flatSubEntity('transaction','transaction');
        $foreignEntityAdapter->flatSubEntity('asset','asset');
        $foreignEntityAdapter->flatSubEntity('transaction.from_account','AAA'); //doesn't work
        $foreignEntityAdapter->flatSubEntity('transaction.to_account','BBB'); //doesn't work


        $foreignEntityAdapter->adaptToLocalVocabulary($openSeaVocabulary);
        $foreignEntityAdapter->populate(100);



        //dd($openSeaSynch->return2dArray());
        $display = '';

        foreach ($foreignEntityAdapter->return2dArray() as $value){

            if (!array_key_exists('f:asset.asset_contract', $value)) continue ;

            $source =  $value[BlockchainEventFactory::EVENT_SOURCE_ADDRESS]['address'] ;
            $destination = $value[BlockchainEventFactory::EVENT_DESTINATION_VERB]['address'];
            $contract = $value['f:asset.asset_contract']['address'];

            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES] = array();
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] =$source ;
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $destination;

            $trackedArray[BlockchainImporter::TRACKER_CONTRACTIDS][] = $contract;

            //correct the format
            $value[BlockchainEventFactory::EVENT_SOURCE_ADDRESS] = $source ;
            $value[BlockchainEventFactory::EVENT_DESTINATION_VERB] = $destination ;
            $value[BlockchainEventFactory::EVENT_DESTINATION_SIMPLE_VERB ] =  $destination  ;
            $value[BlockchainEventFactory::EVENT_CONTRACT] =  $contract  ;
            $value[BlockchainContractFactory::TOKENID] =  $value['f:asset.token_id']  ;



            $value[BlockchainEventFactory::EVENT_BLOCK_TIME] =   date("U",strtotime($value['timestamp']));
            $value['timestamp'] = date("U",strtotime($value['timestamp']));

            //todo add blocktime



            $txHash = $value[Blockchain::$txidConceptName];

            $other = $value ;

            $transactionData = $trackedArray + $other ;

            $entityArray[] = $entity = new ForeignEntity($txHash, $transactionData, $foreignEntityAdapter, $txHash,SandraManager::getSandra());

        }










        $formattedForeign->addNewEtities($entityArray,array());

        return $formattedForeign ;



    }

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

    public static function getChainUrl(): String
    {

       if (!isset(self::$chainUrl)){

           self::$chainUrl = self::$defaultChainUrl ;

       }

       return self::$chainUrl;


    }



}