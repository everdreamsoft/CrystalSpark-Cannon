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
use CsCannon\DisplayManager;
use CsCannon\SandraManager;
use CsCannon\Displayable;
use CsCannon\Token;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

abstract class  BlockchainAddress extends Entity implements Displayable
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


    /**
     * @param BlockchainContract[] $blockchainContracts
     * @param int $limit
     * @param int $offset
     * @return Balance
     */
    public function getBalanceForContract(array $blockchainContracts, $limit=100, $offset=0):Balance{


        $collectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $collectionFactory->populateLocal();



        $dataSource =  $this->getDataSource();
        $balance = $dataSource::getBalanceForContract($this,$blockchainContracts,$limit,$offset);


        $this->balance = $balance ;


        return $balance ;


    }

    abstract public function getBlockchain():Blockchain;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->address = $this->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        $this->balance = new Balance();

    }

    public function setAddress($address){

        $this->address = $address;


    }

    public function getAddress(){

        return $this->address ;


    }


    /**
     * @return BlockchainDataSource
     */
    public function getDataSource(): BlockchainDataSource
    {

        if (is_null($this->dataSource)){
            $this->dataSource = $this->getDefaultDataSource();

        }

        return $this->dataSource ;
    }

    public function setDataSource(BlockchainDataSource $dataSource)
    {

        $this->dataSource = $dataSource ;

        return $this ;
    }

    public function returnArray($displayManager)
    {


        return $this->getAddress() ;
    }

    public function display(): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        return $this->displayManager ;
    }




}