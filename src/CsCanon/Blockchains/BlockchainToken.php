<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains;



use CsCanon\Asset;
use SandraCore\Entity;

abstract class BlockchainToken extends Entity
{

   protected $name ;
    protected static $isa ;
    protected static $file ;


    public function bindToAsset(Asset $asset){

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb,$asset,null);


    }

    public function getJoinedAssets(Asset $asset){

       // $this->getJoined(BlockchainTokenFactory::$joinAssetVerb);


    }













}