<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;



use CsCannon\Asset;
use SandraCore\Entity;
use SandraCore\System;

abstract class BlockchainToken extends Entity
{

   protected $name ;
    protected static $isa ;
    protected static $file ;
    public $tokenId ;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {

        if(isset($sandraReferencesArray[BlockchainTokenFactory::$mainIdentifier])) {
            $this->tokenId = $sandraReferencesArray[BlockchainTokenFactory::$mainIdentifier];
        }

        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
    }


    public function bindToAsset(Asset $asset){

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb,$asset,null);


    }

    public function getJoinedAssets(Asset $asset){

       // $this->getJoined(BlockchainTokenFactory::$joinAssetVerb);


    }

    public function getId(){

        return $this->name ;


    }













}