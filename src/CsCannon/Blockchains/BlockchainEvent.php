<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;



use CsCannon\Asset;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use SandraCore\Entity;
use SandraCore\System;

class BlockchainEvent extends Entity
{

   protected $name ;
    protected static $isa ;
    protected static $file ;



    public function bindToToken(BlockchainToken $token){

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb,$token,null);


    }

    public function getJoinedAssets(\CsCannon\Asset $asset){

       // $this->getJoined(BlockchainTokenFactory::$joinAssetVerb);


    }

     public function getSourceAddress(){

        $source= $this->getJoinedEntities(BlockchainEventFactory::EVENT_SOURCE_ADDRESS);
         if (is_null($source)) return 'null';
        $source = reset($source); //take the first source
        /** @var Entity $source */
        $address = $source->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

         return $address;

     }

    public function getDestinationAddress(){

        $destination= $this->getJoinedEntities(BlockchainEventFactory::EVENT_DESTINATION_VERB);
        if (is_null($destination)) return 'null';
        $destination = reset($destination); //take the first destination
        /** @var Entity $source */
        $address = $destination->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        return $address;

    }

    public function getBlockchainContract(){

        $contract= $this->getJoinedEntities(BlockchainEventFactory::EVENT_CONTRACT);
        $contract = reset($contract); //take the first destination
        /** @var Entity $source */

        if (is_null($contract)){

            dd('null contract');
        }
        //$fullContract = $contract->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        return $contract;

    }

    public function getTokenId(){

        $tokenId = $this->getBrotherReference(BlockchainEventFactory::EVENT_CONTRACT,null,BlockchainContractFactory::TOKENID) ;
        $tokenId = reset($tokenId);


        return $tokenId;

    }



    public function __set($name, $value)
    {
        echo "Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
    }















}