<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;





use App\AssetCollectionFactory;
use App\AssetFactory;
use CsCanon\Blockchains\Blockchain;
use CsCanon\Blockchains\BlockchainEventFactory;
use CsCanon\Blockchains\Ethereum\EthereumEvent;

class XcpEventFactory extends BlockchainEventFactory
{


    public static $className = 'CsCanon\Blockchains\Counterparty\XcpEvent' ;
    public static $isa = 'blockchainEvent';
   // protected static $isa = 'blockchainEvent';



    public function __construct(){

        //Verify data validity


        parent::__construct();

        $this->generatedEntityClass = static::$className ;
        $this->setFilter(self::ON_BLOCKCHAIN_EVENT,XcpBlockchain::NAME);



    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC')
    {



        $return = parent::populateLocal($limit, $offset, $asc);

        $sandra = app('Sandra')->getSandra();

        /** @var Blockchain $blockchain */

        $addressFactory = new XcpAddressFactory();
        $contractFactory = new XcpContractFactory();
        $assetFactory = new AssetFactory();

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS,$addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB,$addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT,$contractFactory);

        $this->joinPopulate();

        $assetFactory = new AssetFactory($sandra);
        $assetCollection = new AssetCollectionFactory($sandra);





        //on counterparty we join the asset factory to the contract
        $contractFactory->joinAsset($assetFactory);
        $contractFactory->joinPopulate();

        $this->joinPopulate();
        $assetFactory->getTriplets();
        $assetFactory->joinCollection($assetCollection);

        $assetFactory->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);



        return $return ;
    }











}