<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;






use SandraCore\Entity;

class  BlockchainContract extends Entity
{

    public function getCollections(){


        return $this->getJoinedEntities(BlockchainContractFactory::JOIN_COLLECTION);


    }










}