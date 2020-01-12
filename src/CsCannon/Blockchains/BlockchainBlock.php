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
use CsCannon\Token;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

 class  BlockchainBlock extends Entity
{

    public function getTimestamp(){

    return $this->get(BlockchainBlockFactory::BLOCK_TIMESTAMP);

    }

     public function getId(){

         return $this->get(BlockchainBlockFactory::INDEX_SHORTNAME);

     }




 }