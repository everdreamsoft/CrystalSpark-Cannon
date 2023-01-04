<?php

namespace CsCannon\Blockchains;

use CsCannon\Blockchains\Interfaces\UnknownStandard;
use CsCannon\Displayable;
use CsCannon\DisplayManager;
use CsCannon\Orb;
use CsCannon\OrbFactory;
use CsCannon\SandraManager;
use CsCannon\Tools\BalanceBuilder;
use Matrix\Exception;
use SandraCore\Entity;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */
class BlockchainEvent extends Entity implements Displayable
{

    protected $name;
    protected static $isa;
    protected static $file;
    public $displayManager;

    const DISPLAY_TXID = 'txId';
    const DISPLAY_VALID = 'validity';
    const DISPLAY_SOURCE_ADDRESS = 'source';
    const DISPLAY_DESTINATION_ADDRESS = 'destination';
    const DISPLAY_CONTRACT = 'contract';
    const DISPLAY_QUANTITY = 'quantity';
    const DISPLAY_ADAPTED_QUANTITY = 'adaptedQuantity';
    const DISPLAY_TIMESTAMP = 'timestamp';
    const DISPLAY_TIMESTAMP_LEGACY = 'legacy';
    const DISPLAY_BLOCKCHAIN = 'blockchain';
    const DISPLAY_BLOCKCHAIN_NETWORK = 'network_name';
    const DISPLAY_BLOCK_ID = 'blockHeight';
    const BLOCKCHAIN_EVENT_TYPE_VERB = 'blockchainEventType';


    public function bindToToken(BlockchainToken $token)
    {

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb, $token, null);


    }

    public function getBlockchainName(): array
    {

        $sc = $this->system->systemConcept;

        $blockchainsUnids = $this->subjectConcept->tripletArray[$sc->get(BlockchainEventFactory::ON_BLOCKCHAIN_EVENT)];
        $return = array();

        foreach ($blockchainsUnids ?? array() as $blockchainUnid) {

            $return[] = $sc->getSCS($blockchainUnid);

        }

        return $return;


    }

    public function getJoinedAssets(\CsCannon\Asset $asset)
    {

        // $this->getJoined(BlockchainTokenFactory::$joinAssetVerb);


    }

    public function getSourceAddress(): ?BlockchainAddress
    {

        $source = $this->getJoinedEntities(BlockchainEventFactory::EVENT_SOURCE_ADDRESS);
        if (is_null($source)) return null;
        $source = reset($source); //take the first source
        /** @var BlockchainAddress $source */
        return $source;

    }


    public function getDestinationAddress()
    {

        $destination = $this->getJoinedEntities(BlockchainEventFactory::EVENT_DESTINATION_VERB);
        if (is_null($destination)) return null;
        /** @var BlockchainAddress $destination */
        $destination = reset($destination); //take the first destination

        return $destination;

    }

    public function getBlockchainContract(): ?BlockchainContract
    {

        $contract = $this->getJoinedEntities(BlockchainEventFactory::EVENT_CONTRACT);
        if (!$contract) return null;
        $contract = reset($contract); //take the first destination
        /** @var Entity $source */

        if (is_null($contract)) {

            SandraManager::dispatchError($this->system, 4, 3, "Event  has no contract", $this);
        }
        //$fullContract = $contract->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        return $contract;

    }

    public function getTxId(): ?string
    {


        return $this->get(Blockchain::$txidConceptName);

    }

    public function getSpecifier(): BlockchainContractStandard
    {
        $tokenData = null;
        $tokenId = null;

        $brotherEntArray = $this->getBrotherEntity(BlockchainEventFactory::EVENT_CONTRACT);


        if (!is_null($brotherEntArray)) {
            $tokenDataEntity = end($brotherEntArray);

            $tokenId = $tokenDataEntity->get("tokenId") ?? null;
            $tokenData = $tokenDataEntity->entityRefs ?? null;
        }


        $contract = $this->getBlockchainContract();
//        $standards = $contract->getStandard();
        $standards = $contract->getJoinedEntities(BlockchainContractFactory::CONTRACT_STANDARD);
        /** @var BlockchainContractStandard $standard */
        $standard = end($standards);

        if (isset($standard)) {
            if(!is_null($tokenId)){
                return $standard::init($tokenId);
            }
            $instance = $standard::init();
            $instance->setTokenPath($tokenData);
            return $instance;
        }
        return UnknownStandard::init();
    }


    public function __set($name, $value)
    {

        $this->data[$name] = $value;
    }

    public function getBlock(): BlockchainBlock
    {
        $block = $this->getJoinedEntities(BlockchainEventFactory::EVENT_BLOCK);
        $block = end($block);
        return $block;
    }

    public function getBlockTimestamp($chain = "")
    {
        $block = $this->getBlock();
        return $block ? $block->getTimestamp($chain) : "";
    }

    public function setSourceContract(BlockchainContract $contract)
    {

        $this->setBrotherEntity(BlockchainEventFactory::EVENT_CONTRACT, $contract, null);
    }

    public function getQuantity()
    {

        return $this->get(BlockchainEventFactory::EVENT_QUANTITY);
    }

    public function isValid()
    {
        $valid = $this->getBrotherEntity(BalanceBuilder::PROCESS_STATUS_VERB);

        //no validity status
        if (!$valid) return null;

        $valid = reset($valid);

        /** @var Entity $valid */

        if ($valid->targetConcept->getShortname() == BalanceBuilder::PROCESS_STATUS_VALID) return true;
        if ($valid->targetConcept->getShortname() == BalanceBuilder::PROCESS_STATUS_INVALID) return false;

        return null;

    }

    public function getError()
    {
        $valid = $this->isValid();

        if ($valid === true or $valid === null) return null;

        if ($valid === false) {
            $validEntity = $this->getBrotherEntity(BalanceBuilder::PROCESS_STATUS_VERB);
            /**@var Entity $validEntity * */
            $validEntity = reset($validEntity);
            return $validEntity->getReference(BalanceBuilder::PROCESSOR_ERROR)->refValue;
        }


    }


    public function returnArray($displayManager, $withOrbs = true)
    {

        $blockchains = $this->getBlockchainName();
        $blockchain = end($blockchains);


        $return[self::DISPLAY_TXID] = $this->get(Blockchain::$txidConceptName);

        if ($this->isValid() !== null) {
            $return[self::DISPLAY_VALID] = $this->isValid() ? 'valid' : 'invalid';
            if ($this->isValid() === false) {
                $return['error'] = $this->getError();
            }
        }

        $return[self::DISPLAY_BLOCKCHAIN] = $blockchain;
        $return[self::DISPLAY_SOURCE_ADDRESS] = $this->getSourceAddress()->display()->return();
        $return[self::DISPLAY_DESTINATION_ADDRESS] = $this->getDestinationAddress()->display()->return();
        try {
            $return[self::DISPLAY_CONTRACT] = $this->getBlockchainContract()->display($this->getSpecifier())->return();
            $sp = $this->getSpecifier();
        } catch (\Exception $e) {
            $return[self::DISPLAY_CONTRACT]['address'] = $this->getBlockchainContract()->display()->return();
            $return[self::DISPLAY_CONTRACT]['standard'] = $this->getBlockchainContract()->getStandard()->getStandardName();
            $return[self::DISPLAY_CONTRACT]['error'] = "event failed to comply " . $e->getMessage();
            //  dd($return[self::DISPLAY_CONTRACT]);

        }

        //force this blockchain into contract
        $return[self::DISPLAY_CONTRACT]['blockchain'] = $blockchain;

        $return[self::DISPLAY_ADAPTED_QUANTITY] = NULL;
        //does it have adapted quantity ?
        if ($this->getBlockchainContract()->decimals) {
            $quantity = $this->get(BlockchainEventFactory::EVENT_QUANTITY);
            $adaptedQuantity = $quantity;
            if ($this->getBlockchainContract()->decimals > 0) {
                $adaptedQuantity = $quantity / pow(10, $this->getBlockchainContract()->decimals);
            }
            $return[self::DISPLAY_ADAPTED_QUANTITY] = $adaptedQuantity;
        }

        $return[self::DISPLAY_QUANTITY] = $this->get(BlockchainEventFactory::EVENT_QUANTITY);

        $return[self::DISPLAY_TIMESTAMP] = $this->getBlockTimestamp($blockchain);
        $return[self::DISPLAY_BLOCK_ID] = $this->getBlock()->getId();

        //autofixer if blocktime doens't exist for block
        if (!$return[self::DISPLAY_TIMESTAMP]) { //blocktime not on block
            if ($this->get(BlockchainEventFactory::EVENT_BLOCK_TIME) > 1) { //legacy blocktime exist
                $block = $this->getBlock();
                $block->setTimestamp($this->get(BlockchainEventFactory::EVENT_BLOCK_TIME), $blockchain);
                $return[self::DISPLAY_TIMESTAMP] = $this->get(BlockchainEventFactory::EVENT_BLOCK_TIME);
            }

        }


        $return[self::DISPLAY_TIMESTAMP_LEGACY] = $this->get(BlockchainEventFactory::EVENT_BLOCK_TIME);


        $contract = $this->getBlockchainContract();
        $collections = $contract->getCollections();


        //here we are building to much factories
        if (is_array($collections) && $this->displayManager->params['withOrbs']) {
            $orbFactory = new OrbFactory();
            $orbArray = $orbFactory->getOrbFromSpecifier($this->getSpecifier(), $contract, reset($collections));

            foreach ($orbArray ? $orbArray : array() as $orb) {
                /**@var Orb $orb */
                $orbArray = $orb->getAsset()->display()->return();
                $orbArray['asset'] = $orb->getAsset()->display()->return();
                $orbArray['imageUrl'] = $orb->getAsset()->imageUrl;
                $orbArray['collection']['name'] = $orb->assetCollection->name;
                $orbArray['collection']['id'] = $orb->assetCollection->getId();
                $return['orbs'][] = $orbArray; //legacy support
                // $return['orbs'][] = $orb->getAsset()->display()->return();

            }
        }

        return $return;
    }

    public function display($withOrbs = true): DisplayManager
    {
        if (!isset($this->displayManager)) {
            $this->displayManager = new DisplayManager($this);
        }
        $this->displayManager->params['withOrbs'] = $withOrbs;

        return $this->displayManager;
    }
}
