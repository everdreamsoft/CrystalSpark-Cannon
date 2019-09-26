<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Ethereum\EthereumToken;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class EthereumAddress extends BlockchainAddress
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumAddress' ;




    public function getBalance():Balance{


       // dd($this->getAddress());



        $finalArray = array();

        //Xchain
        $foreignAdapter = new ForeignEntityAdapter("https://api.opensea.io/api/v1/assets/?format=json&order_by=current_price&order_direction=a&limit=300&owner=".$this->getAddress(),'assets',SandraManager::getSandra());

        $assetVocabulary = array('image_url'=>'image',
            'assetName'=>'assetName',
            'name'=>'name',



        );



        $balance = $this->balance ;

        $foreignAdapter->flatSubEntity('asset_contract','contract');
        $foreignAdapter->adaptToLocalVocabulary($assetVocabulary);
        $foreignAdapter->populate();

        $system = SandraManager::getSandra();
        $collectionContractsArray = array();

        $collectionFactory = new AssetCollectionFactory($system);
        $collectionFactory->populateLocal();

        $collectionAssetCount = array();
        $return['collections'] = array();

        $contractFactory = new EthereumContractFactory();
        //careful here we are loading all contract onto memory

        //todo fix this
        $contractFactory->populateLocal();



        //we are using open sea
        $openSeaImporter = new OpenSeaImporter(SandraManager::getSandra(),$collectionFactory);
        $balance = $openSeaImporter->getBalance($this,100,0);


        $return['collections']  = $finalArray;

        $this->balance = $balance ;


        return $balance ;







}

    public function createForeign(){



        dd("creating foreign");



    }


    public function getBlockchain(): Blockchain
    {
        return new EthereumBlockchain();
    }
}