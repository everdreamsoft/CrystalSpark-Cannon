<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use CsCannon\Blockchains\Ethereum\EthereumContract;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use SandraCore\displayer\DisplayType;
use SandraCore\Entity;
use SandraCore\System;

class AssetCollectionFactory extends \SandraCore\EntityFactory
{


    protected static $isa = 'assetCollection' ;
    protected static $file = 'assetCollectionFile' ;
    protected static $className = 'CsCannon\AssetCollection' ;

    public $id = 'collectionId';
    const METADATASOLVER_VERB = 'has';
    const METADATASOLVER_TARGET = 'metaDataSolver';

    public static $staticInstance = null ;
    public static $staticSolvers = null ;

    const IMAGE_EXTENSION = 'imageExtension';
    const MAIN_IMAGE = 'imageUrl';
    const MAIN_NAME = 'name';





    public function __construct(System $sandra){

        parent::__construct(static::$isa,static::$file,$sandra);


        $this->generatedEntityClass = static::$className ;


    }

    public static function getStaticCollection(): AssetCollectionFactory{

        if (self::$staticInstance == null) {
            self::$staticInstance = new AssetCollectionFactory(SandraManager::getSandra());
            self::$staticInstance->populateLocal();
        }



        return self::$staticInstance ;

    }

    private static function getSolverData (){

        $actualSandra = SandraManager::getSandra() ;

        if (self::$staticSolvers == null or $actualSandra->instanceId != self::$staticSolvers->system->instanceId) {
            self::$staticSolvers = new MetadataSolverFactory(SandraManager::getSandra());

        }



        return self::$staticSolvers ;

    }

    public function create($id,$dataArray,$assetSolver = null):?AssetCollection{

        $sandra = SandraManager::getSandra() ;

        //first we verify if this id is taken

        $collectionFactoryControl = new AssetCollectionFactory(SandraManager::getSandra());

        $verif = $collectionFactoryControl->get($id);

        if (isset($verif))  SandraManager::dispatchError($sandra,1,3,'collectionExists',$this);


        $dataToSave = $dataArray ;
        $dataToSave[$this->id] = $id ;

        if ($assetSolver == null){

            $assetSolver = LocalSolver::getEntity();
        }

        return $this->createNew($dataToSave,[self::METADATASOLVER_VERB=>$assetSolver]);


    }

    public function get($id):?AssetCollection{



        return $this->first($this->id,$id);


    }

    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC')
    {
        $output = parent::populateLocal($limit, $offset, $asc);

        $this->getTriplets();
        $this->joinFactory(self::METADATASOLVER_VERB,self::getSolverData());
        $this->joinPopulate();

        //set the solvers
        foreach ($this->entityArray ? $this->entityArray : array() as $collectionEntity) {

            /** @var AssetCollection $collectionEntity */
            //$solvers = $collectionEntity->getJoinedEntities(self::METADATASOLVER_VERB);
            //$collectionEntity->setSolvers($solvers);

        }
    }





    public function getOrCreate($collectionId, AssetSolver $assetSolver = null):AssetCollection{

        if ($assetSolver == null){

            $assetSolver = LocalSolver::getEntity();
        }

        $enity  = $this->getOrCreateFromRef($this->id,$collectionId);

        /** @var AssetCollection $enity */
        $enity->setSolver($assetSolver);

        return $enity ;




    }






    public function createFromOpenSeaEntity(Entity $openSeaEntity, EthereumContract $contract){

        $data['name'] = $openSeaEntity->get('contract.name');
        $data['symbol'] = $openSeaEntity->get('contract.symbol');
        $data['imageUrl'] = $openSeaEntity->get('contract.image_url');
        $data['description'] = $openSeaEntity->get('contract.description');
        $data['externalLink'] = $openSeaEntity->get('contract.external_link');

        $data['collectionId'] = $openSeaEntity->get('contract.address');



        //$links['source'] = 'openSea';
        //$links['hasContract'] = 'openSea'; //TODO important

        $newCollection = $this->createNew($data);

        $contract->setBrotherEntity(BlockchainContractFactory::JOIN_COLLECTION,$newCollection,null);


        return $newCollection ;



        //$this->createNew()




    }

    public function getDisplay($a,$b=null,$c=null,DisplayType $displayType=null){


        //There is an issue here there is unclarity between the vocabulary. In fact vocabulary is to map foreign to local
        // we should be able to translate also the Keys into something else than local shortname


        $assetVocabulary = array('collectionId'=>'id',
            'name'=>'name',
            'description'=>'description',
            'imageUrl'=>'image',


        );


        $todisplay = array_keys($assetVocabulary);

       return parent::getDisplay('array',$todisplay,$assetVocabulary,$displayType);



        //$this->createNew()





    }


}