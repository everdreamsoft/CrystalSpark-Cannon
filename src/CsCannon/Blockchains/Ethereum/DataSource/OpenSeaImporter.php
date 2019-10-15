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
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
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
class OpenSeaImporter extends BlockchainDataSource
{

    public $sandra ;



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







        /*
        foreach ($resultArray as $result) {


            //add tracker
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES] = array();

            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['source_address'] ;
            $trackedArray[BlockchainImporter::TRACKER_ADDRESSES][] = $result['destination_address'] ;



            $hash = $result['tx_hash'];

            $quantity = $result['quantity'];
            $transactionData = array("txHash" => "$hash",
                "memo" => $result['memo'],
                "quantity" => $quantity,
                Blockchain::$txidConceptName => $hash,
                BlockchainEventFactory::EVENT_SOURCE_ADDRESS => $result['source_address'],
                "destinationAddress" => $result['destination_address'],
                "tokenId" => $result['asset'],
                "blockIndex" => $result['block_index'],
                "blockTime" => $result['block_time'],
                "contract" => $result['asset'],


            );

            //add tracker
            $transactionData[BlockchainImporter::TRACKER_CONTRACTIDS][] = $result['asset'] ;


            //$transactionData[BlockchainImporter::TRACKER_BLOCKID][] =  $result['block_index'] ;


            $transactionData = $transactionData + $trackedArray ;




        }*/



        $formattedForeign->addNewEtities($entityArray,array());

        return $formattedForeign ;



    }

    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        $foreignAdapter = new ForeignEntityAdapter("https://api.opensea.io/api/v1/assets/?format=json&order_by=current_price&order_direction=a&limit=300&owner=".$address->getAddress(),'assets',SandraManager::getSandra());

        $assetVocabulary = array('image_url'=>'image',
            'assetName'=>'assetName',
            'name'=>'name',
        );

        $foreignAdapter->flatSubEntity('asset_contract','contract');
        $foreignAdapter->adaptToLocalVocabulary($assetVocabulary);
        $foreignAdapter->populate();
       // $foreignAdapter->dumpMeta();
        $contractFactory = new EthereumContractFactory();

        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();

         self::$localCollections = $collectionFactory ;
        $balance = new Balance();

        //opensea API ruleset

        foreach ($foreignAdapter->entityArray as $entity){

            /** @var ForeignEntity $entity */

            $contractAddress = $entity->get('contract.address');
            $standard = $entity->get('contract.schema_name');
            //die("standard $standard");
            $contractStandard = new UnknownStandard();

            if ($standard == "ERC721") $contractStandard = new ERC721();


            //on opensea one contract = 1 collection
            if(!isset($collectionArray[$contractAddress])){

                $collection = $collectionFactory->first($collectionFactory->id,$contractAddress);

                if (is_null($collection)){

                    $contractEntity = $contractFactory->get($contractAddress,true,$contractStandard);
                    $collection = $collectionFactory->createFromOpenSeaEntity($entity,$contractEntity);

                }
                $collectionArray[$contractAddress] = $collection;

            }
            $collection = $collectionArray[$contractAddress] ;

            if(!isset( $collectionContractsArray[$contractAddress])){
                $contract['address'] = $contractAddress;
                $collectionContractsArray[$contractAddress][] = $contract;
            }


            //$contract['address'] = $contractAddress;
            if(!isset($collectionAssetCount[$contractAddress])){
                $collectionAssetCount[$contractAddress] = 0;
            }
            $collectionAssetCount[$contractAddress]++;

            /** @var AssetCollection $collection */



         //   $assetEntity['image'] = $entity->get('image');
           // $assetEntity['assetId'] = $contractAddress.'-'.$entity->get('token_id');

            $contractFactory->populateLocal();
            $ethContract = $contractFactory->get($contractAddress);

            $standard = new ERC721();

            $standard->setTokenId($entity->get('token_id'));
            $balance->addContractToken($ethContract,$standard,1);






        }
        return $balance ;

    }

    public function createCollectionFromOS($openseaEntity){






    }


}