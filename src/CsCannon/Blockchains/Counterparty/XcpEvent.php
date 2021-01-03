<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.03.2019
 * Time: 12:24
 */

namespace CsCannon\Blockchains\Counterparty;





use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainTokenFactory;

class XcpEvent extends BlockchainEvent
{

    protected static $isa = 'xcpEvent';
    protected static $file = 'blockchainEventFile';


    //override the token id on counterparty since work a bit differently. The token is the contract
    public function getTokenId(){

        $tokenEntity = $this->getJoinedEntities(BlockchainEventFactory::EVENT_CONTRACT);

        //get the first token
        if (!is_array($tokenEntity)) return null ;
        $tokenEntity = reset($tokenEntity);

        /** @var XcpToken $tokenEntity */

        $tokenId =$tokenEntity->get(BlockchainTokenFactory::$mainIdentifier);


        return $tokenId;

    }


}