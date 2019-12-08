<?php

use CsCannon\Orb;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;




use CsCannon\DisplayManager;

use CsCannon\OrbFactory;
use CsCannon\SandraManager;
use CsCannon\Displayable;
use SandraCore\Entity;
use SandraCore\System;

class BlockchainEvent extends Entity implements Displayable
{

   protected $name ;
    protected static $isa ;
    protected static $file ;
    public $displayManager ;

    const DISPLAY_TXID = 'txId';
    const DISPLAY_SOURCE_ADDRESS = 'source';
    const DISPLAY_DESTINATION_ADDRESS = 'destination';
    const DISPLAY_CONTRACT = 'contract';
    const DISPLAY_QUANTITY = 'quantity';
    const DISPLAY_TIMESTAMP = 'timestamp';
    const DISPLAY_BLOCKCHAIN = 'blockchain';
    const DISPLAY_BLOCKCHAIN_NETWORK = 'network_name';



    public function bindToToken(BlockchainToken $token){

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb,$token,null);


    }

    public function getJoinedAssets(\CsCannon\Asset $asset){

       // $this->getJoined(BlockchainTokenFactory::$joinAssetVerb);


    }

     public function getSourceAddress():?BlockchainAddress{

        $source= $this->getJoinedEntities(BlockchainEventFactory::EVENT_SOURCE_ADDRESS);
         if (is_null($source)) return null;
        $source = reset($source); //take the first source
        /** @var BlockchainAddress $source */
         return $source;




     }

    public function getDestinationAddress(){

        $destination= $this->getJoinedEntities(BlockchainEventFactory::EVENT_DESTINATION_VERB);
        if (is_null($destination)) return null;
        $destination = reset($destination); //take the first destination
        /** @var BlockchainAddress $source */


        return $destination;

    }

    public function getBlockchainContract():?BlockchainContract{

        $contract= $this->getJoinedEntities(BlockchainEventFactory::EVENT_CONTRACT);
        $contract = reset($contract); //take the first destination
        /** @var Entity $source */

        if (is_null($contract)){

            SandraManager::dispatchError($this->system,4,3,"Event  has no contract",$this);
        }
        //$fullContract = $contract->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        return $contract;

    }

    public function getSpecifier(){

        //$tokenData = $this->getBrotherRefwerence(BlockchainEventFactory::EVENT_CONTRACT,null,BlockchainContractFactory::TOKENID) ;
        //if(!is_array($tokenData)) { return null ;}
        //;
        $tokenData = null ;

        $brotherEntArray = $this->getBrotherEntity(BlockchainEventFactory::EVENT_CONTRACT);


        if (!is_null($brotherEntArray)) {
            $tokenDataEntity  = end($brotherEntArray);
            $tokenData = $tokenDataEntity->entityRefs;

        }


        $contract = $this->getBlockchainContract();
        $standards = $contract->getStandard();

        /** @var BlockchainContractStandard $standard */

        if (isset($standards)){
            /** @var BlockchainContractStandard $instance */
            $instance = $standards::init();
            $instance->setTokenPath($tokenData);
            return  $instance ;

        }


        return null;

    }



    public function __set($name, $value)
    {
        echo "Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
    }


    public function returnArray($displayManager)
    {
        $return[self::DISPLAY_TXID] = $this->get(Blockchain::$txidConceptName);
        $return[self::DISPLAY_SOURCE_ADDRESS] = $this->getSourceAddress()->display()->return();
        $return[self::DISPLAY_DESTINATION_ADDRESS] = $this->getDestinationAddress()->display()->return();
        $return[self::DISPLAY_CONTRACT] = $this->getBlockchainContract()->display($this->getSpecifier())->return();
        $return[self::DISPLAY_QUANTITY] = $this->get(BlockchainEventFactory::EVENT_QUANTITY);
        $return[self::DISPLAY_TIMESTAMP] = $this->get(BlockchainEventFactory::EVENT_BLOCK_TIME);
        $return[self::DISPLAY_BLOCKCHAIN] = $this->getBlockchainContract()->getBlockchain()::NAME ;

       $contract =  $this->getBlockchainContract();
       $collections = $contract->getCollections();
        $sp = $this->getSpecifier();

       //here we are building to much factories
        if(is_array($collections)) {
            $orbFactory = new OrbFactory();
            $orbArray = $orbFactory->getOrbFromSpecifier($this->getSpecifier(), $contract, reset($collections));

            foreach ($orbArray ? $orbArray : array() as $orb) {
                /**@var Orb $orb */
                $orbArray = $orb->getAsset()->display()->return();
                $orbArray['asset'] = $orb->getAsset()->display()->return();
                $orbArray['collection']['name'] = $orb->assetCollection->name;
                $orbArray['collection']['id'] = $orb->assetCollection->getId();
                $return['orbs'][] = $orbArray ; //legacy support
               // $return['orbs'][] = $orb->getAsset()->display()->return();

            }
        }

        return $return ;
    }

    public function display(): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }

        return $this->displayManager ;
    }
}