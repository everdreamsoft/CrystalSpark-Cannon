<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;






use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\DataSource\CrystalSuiteDataSource;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\ContractMetaData;
use CsCannon\Displayable;
use CsCannon\DisplayManager;
use SandraCore\CommonFunctions;
use SandraCore\DatabaseAdapter;
use SandraCore\Entity;
use SandraCore\System;

abstract class  BlockchainContract extends Entity Implements Displayable
{

    abstract  function getBlockchain():Blockchain;
    public $id ;
    public $displayManager ;
    public $decimals = null  ;
    public $metadata = null  ;

    const DISPLAY_ID = 'contract';

    const EXPLICIT_TOKEN_LISTING_SHORTNAME = 'explicitListing'; // explicit token listing is used when one asset point to multiple contract's token ID. For example SOG one card multiple token id
    const ALIAS_SHORTNAME = 'alias'; // fullText Alias
    public static $defaultDataSource = CrystalSuiteDataSource::class ;
    public $dataSource ;

     public function getDefaultDataSource():BlockchainDataSource{

         /** @var BlockchainDataSource $defaultDataSource */
         $newClass = new self::$defaultDataSource() ;

         return $newClass ;

     }


    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system){

        /** @var System $system */

        if (!isset($sandraReferencesArray[BlockchainContractFactory::MAIN_IDENTIFIER]) &&
            !isset($sandraReferencesArray[$system->systemConcept->get( BlockchainContractFactory::MAIN_IDENTIFIER)])){
            $system->systemError(1,self::class,3,"contract must have an id");

        }



        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->id = $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);
        $this->decimals = $this->get(BlockchainContractFactory::DECIMALS);
        $this->metadata = new ContractMetaData($this);


    }

    public function getStandard():?BlockchainContractStandard{



        $return = $this->getJoinedEntities(BlockchainContractFactory::CONTRACT_STANDARD);
        if (is_array($return)) $return = end($return);

        return $return ;


    }

    public function setStandard(BlockchainContractStandard $standard){

        $this->setBrotherEntity(BlockchainContractFactory::CONTRACT_STANDARD,$standard,null);

        return $this ;


    }

    /**
     *
     * Get all asset binded to this contract
     *
     * @return Asset[]
     */
    public function getAssets(){

        $assetFactory = new AssetFactory();
        $assetFactory->setFilter(AssetFactory::$tokenJoinVerb,$this);
        $assetFactory->populateLocal();

        return $assetFactory->getEntities();

    }

    public function getCollections(){

        $collectionEntities = null ;
        $this->factory->getTriplets();
        $entitiesArray = $this->getJoinedEntities(BlockchainContractFactory::JOIN_COLLECTION);
        //we keep only collections entities
        foreach($entitiesArray ? $entitiesArray : array() as $entity){
            if ($entity instanceof AssetCollection) $collectionEntities[] = $entity ;

        }

        return $collectionEntities;


    }

    public function setExplicitTokenId($boolean = true){

        return $this->getOrInitReference(self::EXPLICIT_TOKEN_LISTING_SHORTNAME,$boolean);


    }

    public function isExplicitTokenId(){

        $explicit = $this->get(self::EXPLICIT_TOKEN_LISTING_SHORTNAME) ;
        if (is_null($explicit)) return 0 ;

        return $explicit ;


    }



    public function bindToCollection(AssetCollection $collection){

        $this->setBrotherEntity(BlockchainContractFactory::JOIN_COLLECTION,$collection,null);


    }



    public function setAlias($alias):self{

        //verify alias existense
        $verif = new GenericContractFactory();
        $aliasUnid = $this->system->systemConcept->get(self::ALIAS_SHORTNAME);
        $fileUnid = $this->system->systemConcept->get(BlockchainContractFactory::$file);
        $exist = DatabaseAdapter::searchConcept($alias,$aliasUnid,$this->system,'',$fileUnid);


        if ($exist) {
            /** @var BlockchainContract  $lastContract */
            $lastContract = end($exist);
            if (!$lastContract == $this->subjectConcept->idConcept) // the contract we are trying to alias exist and it's not this contract
                $this->system->systemError(0, self::class, 4, 'Contract alias exists ' . $alias);

        }
        $this->getOrInitReference(self::ALIAS_SHORTNAME,$alias);

        return $this ;

    }

    public function getAlias(){

        return $this->get(self::ALIAS_SHORTNAME);

    }

    public function setDivisibility(int $decimals):self{

        $this->decimals = $decimals ;
        $this->getOrInitReference(self::DIVISIBILITY,$decimals);
        return $this ;




    }

    public function getAdaptedDecimals(int $number):?float{

        if (!$this->decimals) return null ;

        $adapted = ($number/(pow(10,$this->decimals)));
        return $adapted ;


    }



    public function getId(){

        return $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);

    }

    public function returnArray($displayManager)
    {
        $return = $this->getId();


        //In case we specified a specifier for the contract like tokenId = 1
        if ($displayManager->dataStore['specifier']){

            $return = array();

            $token = $displayManager->dataStore['specifier'];

            /** @var BlockchainContractStandard $token */

            $return['address'] = $this->getId();
            $return['standard'] = $token->getStandardName() ;
            $return['blockchain'] = $this->getBlockchain()::NAME ;

            $return['token'] = $token->specificatorData ;

        }




        return $return ;
    }

    public function display($specifier = null): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        $this->displayManager->dataStore['specifier'] = $specifier;

        return $this->displayManager ;
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



}