<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;




use CsCannon\Asset;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\SandraManager;
use CsCannon\Token;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

abstract class  BlockchainAddress extends Entity
{

   protected $address ;
   public $assetList = array();
   public $balance ;
    public $dataSource ;

    abstract public function getDefaultDataSource():BlockchainDataSource;

     public function getBalance():Balance{


         $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
         $collectionFactory->populateLocal();



         $dataSource =  $this->getDataSource();
         $balance = $dataSource::getBalance($this,100,0);


         $this->balance = $balance ;


         return $balance ;


     }
    abstract public function getBlockchain():Blockchain;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->balance = new Balance();

    }

    public function setAddress($address){

        $this->address = $address;


    }

    public function getAddress(){

        return $this->address ;


    }


    public function getEvents($limit = 100,$offset=0){


        $blockchain = $this->getBlockchain();

        $eventFactorySender = clone $blockchain->getEventFactory();
        //$eventFactoryReceiver = clone $blockchain->getEventFactory();

        $eventFactorySender->filterBySender($this);

        $eventFactorySender->populateLocal($limit,$offset);
        //$eventFactoryReceiver->populateLocal($limit,$offset);

        print_r($eventFactorySender->getArray());

    }

    public function getDataSource(): BlockchainDataSource
    {

        if (is_null($this->dataSource)){
            $this->dataSource = $this->getDefaultDataSource();

        }

        return $this->dataSource ;
    }




}