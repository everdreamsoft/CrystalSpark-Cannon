<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

class Asset extends \SandraCore\Entity
{

    public $metaDataUrl;
    public $imageUrl;
    public $id ;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {


        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
        //if the factory is foreign we map data to correct concepts ids

        if ($factory instanceof ForeignEntityAdapter){

           $this->imageUrl = $sandraReferencesArray['imageUrl'];
            $this->id = $sandraReferencesArray['assetId'];
            $this->metaDataUrl = $sandraReferencesArray['metaDataUrl'];

        }
        else{

            $this->imageUrl = $this->get(AssetFactory::IMAGE_URL);
            $this->id = $this->get(AssetFactory::ID);
            $this->metaDataUrl =  $this->get(AssetFactory::METADATA_URL);

        }




    }

    public $displayable = array(
        'id'=>'id',
        'image'=>'image',


    );

    public function bindToContract(BlockchainContract $token){


        $this->setBrotherEntity(AssetFactory::$tokenJoinVerb,$token,null);

    }

    public function getContracts(){


        $this->factory->getTriplets();
        //$this->factory->populateBrotherEntities()
        return $this->getJoinedEntities(AssetFactory::$tokenJoinVerb);

    }

    public function getCollections(){

        $this->factory->getTriplets();
        return $this->getJoinedEntities(AssetFactory::$collectionJoinVerb);

    }




    public function joinCollection(BlockchainToken $token){

        $this->setBrotherEntity(AssetFactory::$tokenJoinVerb,$token,null);




    }

    public function getDisplayable(){

      foreach ($this->displayable as $referenceShortname => $referenceTitle){

        $return[$referenceTitle] = $this->get($referenceShortname) ;
      }

      return $return ;

    }

    public function getDisplayableCollection($collectionEntityArray, $simple = 'false'){

        foreach ($this->displayable as $referenceShortname => $referenceTitle){

            $return[$referenceTitle] = $this->get($referenceShortname) ;
        }

        if (is_array($collectionEntityArray))
            foreach ($collectionEntityArray as $collectionEntity){

                $return['collections'][] = $collectionEntity->getDefaultDisplay($simple);

            }



        return $return ;

    }


}