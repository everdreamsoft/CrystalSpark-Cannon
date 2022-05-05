<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;


use CsCannon\Token;
use SandraCore\Entity;

class  BlockchainBlock extends Entity
{

    public function getTimestamp($chain = "")
    {

        $timestamp = $this->get(BlockchainBlockFactory::getTimestampPrefix($chain) . BlockchainBlockFactory::BLOCK_TIMESTAMP);

        if (!$timestamp) {
            $timestamp = $this->get(BlockchainBlockFactory::BLOCK_TIMESTAMP);
        }

        return $timestamp;
    }

    public function getId()
    {
        return $this->get(BlockchainBlockFactory::INDEX_SHORTNAME);
    }

    public function setTimestamp($timestamp, $chain = "")
    {
        return $this->createOrUpdateRef(BlockchainBlockFactory::getTimestampPrefix($chain) . BlockchainBlockFactory::BLOCK_TIMESTAMP, $timestamp);
    }


}
