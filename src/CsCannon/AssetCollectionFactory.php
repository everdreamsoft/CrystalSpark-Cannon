<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
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

    const IMAGE_EXTENSION = 'imageExtension';
    const MAIN_IMAGE = 'imageUrl';





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

    public function get($id){

        return $this->first($this->id,$id);


    }



    public function createFromOpenSeaEntity(Entity $openSeaEntity){

        $data['name'] = $openSeaEntity->get('contract.name');
        $data['symbol'] = $openSeaEntity->get('contract.symbol');
        $data['imageUrl'] = $openSeaEntity->get('contract.image_url');
        $data['description'] = $openSeaEntity->get('contract.description');
        $data['externalLink'] = $openSeaEntity->get('contract.external_link');

        $data['collectionId'] = $openSeaEntity->get('contract.address');

        //$links['source'] = 'openSea';
        //$links['hasContract'] = 'openSea'; //TODO important


        return $this->createNew($data,null);



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