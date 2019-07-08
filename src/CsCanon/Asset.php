<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCanon;

use App\Blockchains\BlockchainToken;
use App\Blockchains\BlockchainTokenFactory;

class Asset extends \SandraCore\Entity
{

    public $displayable = array(
        'id'=>'id',
        'image'=>'image',


    );

    public function bindToToken(BlockchainToken $token){

        $this->setBrotherEntity(AssetFactory::$tokenJoinVerb,$token,null);




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