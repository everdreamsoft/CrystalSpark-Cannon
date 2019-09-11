<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;






use SandraCore\Entity;

abstract class  BlockchainContract extends Entity
{

    abstract  function getBlockchain():Blockchain;

    public function getCollections(){


        return $this->getJoinedEntities(BlockchainContractFactory::JOIN_COLLECTION);


    }

    public function getId(){


        return $this->get(BlockchainContractFactory::MAIN_IDENTIFIER);


    }










}