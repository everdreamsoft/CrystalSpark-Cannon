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
use CsCannon\SandraManager;
use CsCannon\Tests\Displayable;
use SandraCore\Entity;
use SandraCore\System;

class BlockchainEvent extends Entity implements Displayable
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

        $tokenData = $this->getBrotherReference(BlockchainEventFactory::EVENT_CONTRACT,null,BlockchainContractFactory::TOKENID) ;
        if(!is_array($tokenData)) { return null ;}
        ;

        $contract = $this->getBlockchainContract();
        $standards = $contract->getStandard();
        $firstStandard = reset($standards);
        /** @var BlockchainContractStandard $standard */

        if (isset($firstStandard)){
            /** @var BlockchainContractStandard $instance */
            $instance = new $firstStandard();
            $instance->specificatorData = $tokenData ;
            return  $instance ;

        }


        return null;

    }



    public function __set($name, $value)
    {
        echo "Setting '$name' to '$value'\n";
        $this->data[$name] = $value;
    }















}