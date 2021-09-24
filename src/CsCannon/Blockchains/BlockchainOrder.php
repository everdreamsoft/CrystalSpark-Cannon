<?php

namespace CsCannon\Blockchains;


use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
use CsCannon\Orb;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */



use CsCannon\DisplayManager;


use CsCannon\OrbFactory;
use CsCannon\SandraManager;
use CsCannon\Displayable;
use CsCannon\Tools\BalanceBuilder;
use Matrix\Exception;
use SandraCore\Entity;
use SandraCore\System;

class BlockchainOrder extends BlockchainEvent
{

    private $blockchain;

    private $contractToSell;
    private $contractToSellId;

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);


        $this->blockchain = $this->getBlockchain();

        //kusama specifics ? (WHen a an order match a specific spend

    }

    public function getBlockchain():Blockchain{

        $conceptTriplets = $this->subjectConcept->getConceptTriplets();
        $conceptId = $conceptTriplets[$this->system->systemConcept->get(BlockchainOrderFactory::ON_BLOCKCHAIN)] ?? null;
        $lastId = end($conceptId);
        $blockchainName = $this->system->systemConcept->getSCS($lastId);

        return BlockchainRouting::getBlockchainFromName($blockchainName);


    }

    /**
     * @return BlockchainContractStandard|null
     */
    public function getTokenSell(): ?BlockchainContractStandard
    {
        $tokenSell = $this->getJoinedEntities(BlockchainOrderFactory::TOKEN_SELL);
        $tokenSell = end($tokenSell);
        /** @var BlockchainContractStandard $tokenSell */

        $brotherEntArray = $this->getBrotherEntity(BlockchainEventFactory::TOKEN_SELL);

        if (!is_null($brotherEntArray)) {
            $tokenDataEntity  = end($brotherEntArray);
            $tokenData = $tokenDataEntity->entityRefs;
            $tokenSell->setTokenPath($tokenData);
        }


        return $tokenSell ;
    }

    /**
     * @return BlockchainContractStandard|null
     */
    public function getTokenBuy(): ?BlockchainContractStandard
    {
        $tokenBuy = $this->getJoinedEntities(BlockchainOrderFactory::TOKEN_BUY);
        $tokenBuy = end($tokenBuy);

        $brotherEntArray = $this->getBrotherEntity(BlockchainEventFactory::TOKEN_BUY);

        if (!is_null($brotherEntArray)) {
            $tokenDataEntity  = end($brotherEntArray);
            $tokenData = $tokenDataEntity->entityRefs;
            $tokenBuy->setTokenPath($tokenData);
        }




        return $tokenBuy ;
    }





    /**
     * @return BlockchainContract
     */
    public function getContractToSell(): BlockchainContract
    {
        $contractToSell = $this->getJoinedEntities(BlockchainOrderFactory::ORDER_SELL_CONTRACT);
        $this->contractToSell = end($contractToSell);
        return $this->contractToSell ;
    }

    /**
     * @return string
     */
    public function getContractToSellId(): string
    {
        return $this->contractToSellId;
    }

    /**
     * @return string
     */
    public function getContractToSellQuantity(): string
    {
        $toSellQuantity = $this->getReference(BlockchainOrderFactory::REMAINING_SELL) ?? null;

        $toSellQuantity = !is_null($toSellQuantity) ? $toSellQuantity->refValue : $this->getReference(BlockchainOrderFactory::SELL_PRICE)->refValue;

        return $toSellQuantity;
    }

    /**
     * @return BlockchainContract|null
     */
    public function getContractToBuy(): ?BlockchainContract
    {

        $contractToBuy = $this->getJoinedEntities(BlockchainOrderFactory::ORDER_BUY_CONTRACT);
        if (!$contractToBuy) return null ;
        return end($contractToBuy);
    }



    /**
     * @return string
     */
    public function getContractToBuyQuantity(): string
    {
        $toBuyQuantity = $this->getReference(BlockchainOrderFactory::REMAINING_BUY) ?? null;
        return !is_null($toBuyQuantity) ? $toBuyQuantity->refValue : $this->getReference(BlockchainOrderFactory::BUY_AMOUNT)->refValue;
    }

    /**
     * @return string
     */
    public function getTotal(): string
    {
        $total = $this->getReference(BlockchainOrderFactory::REMAINING_TOTAL) ?? null;
        return !is_null($total) ? $total->refValue : $this->getReference(BlockchainOrderFactory::BUY_TOTAL)->refValue;
    }

    /**
     * @return BlockchainAddress
     */
    public function getSource(): BlockchainAddress
    {
        $source = $this->getJoinedEntities(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS);
        return end($source);
    }

    /**
     * @return BlockchainAddress|null
     */
    public function getBuyDestination(): ?BlockchainAddress
    {
        /** @var BlockchainAddress[] $buyDestination */
        $buyDestination = $this->getJoinedEntities(BlockchainOrderFactory::BUY_DESTINATION) ?? null;

        return is_null($buyDestination) ? null : end($buyDestination);
    }


    public function closeOrder()
    {
//        $this->createOrUpdateRef(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CLOSE);
//        return $this->getReference(BlockchainOrderFactory::STATUS)->refValue ?? null;
        $this->setBrotherEntity(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CLOSE, null, true, true);
    }






    /**
     * @param BlockchainToken $token
     */
    public function bindToToken(BlockchainToken $token){

        $this->setBrotherEntity(BlockchainTokenFactory::$joinAssetVerb,$token,null);


    }

    /**
     * @return array
     */
    public function getBlockchainName():array{

        $sc = $this->system->systemConcept ;

        $blockchainsUnids = $this->subjectConcept->tripletArray[$sc->get(BlockchainEventFactory::ON_BLOCKCHAIN_EVENT)];
        $return = array();

        foreach ($blockchainsUnids ?? array() as $blockchainUnid){

            $return[] = $sc->getSCS($blockchainUnid);

        }

        return $return ;


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
            $instance = $standards::init();
            $instance->setTokenPath($tokenData);
            return  $instance ;

        }
        else {
            return UnknownStandard::init();

        }


        return null;

    }



    public function __set($name, $value)
    {

        $this->data[$name] = $value;
    }



    public function getBlockTimestamp()
    {
        return $this->getBlock()->get(BlockchainBlockFactory::BLOCK_TIMESTAMP);
    }

    public function setSourceContract(BlockchainContract $contract)
    {

        $this->setBrotherEntity(BlockchainEventFactory::EVENT_CONTRACT,$contract,null);
    }

    public function getQuantity()
    {

        return $this->get(BlockchainEventFactory::EVENT_QUANTITY);
    }

    public function isValid()
    {
        $valid = $this->getBrotherEntity(BalanceBuilder::PROCESS_STATUS_VERB);

        //no validity status
        if (!$valid) return null ;

        $valid = reset($valid);

        /** @var Entity $valid */

        if($valid->targetConcept->getShortname() == BalanceBuilder::PROCESS_STATUS_VALID) return true ;
        if($valid->targetConcept->getShortname() == BalanceBuilder::PROCESS_STATUS_INVALID) return false ;

        return null ;

    }

    public function getError()
    {
        $valid = $this->isValid() ;

        if ($valid === true or $valid === null) return null ;

        if ($valid === false){
            $validEntity = $this->getBrotherEntity(BalanceBuilder::PROCESS_STATUS_VERB);
            /**@var Entity $validEntity **/
            $validEntity = reset($validEntity);
            return $validEntity->getReference(BalanceBuilder::PROCESSOR_ERROR)->refValue;
        }



    }


    public function returnArray($displayManager,$withOrbs=true)
    {

        $blockchains = $this->getBlockchainName();
        $blockchain = end($blockchains);


        $return[self::DISPLAY_TXID] = $this->get(Blockchain::$txidConceptName);

        if ($this->isValid() !== null){
            $return[self::DISPLAY_VALID] = $this->isValid() ? 'valid' : 'invalid';
            if ($this->isValid() === false) {
                $return['error'] = $this->getError();
            }
        }

        $return[self::DISPLAY_BLOCKCHAIN] = $blockchain ;
        $return[self::DISPLAY_SOURCE_ADDRESS] = $this->getSourceAddress()->display()->return();
        $return[self::DISPLAY_DESTINATION_ADDRESS] = $this->getDestinationAddress()->display()->return();
        try {
            $return[self::DISPLAY_CONTRACT] = $this->getBlockchainContract()->display($this->getSpecifier())->return();
            $sp = $this->getSpecifier();
        }catch (\Exception $e){
            $return[self::DISPLAY_CONTRACT]['address'] = $this->getBlockchainContract()->display()->return();
            $return[self::DISPLAY_CONTRACT]['standard'] = $this->getBlockchainContract()->getStandard()->getStandardName();
            $return[self::DISPLAY_CONTRACT]['error'] = "event failed to comply ".$e->getMessage();
            //  dd($return[self::DISPLAY_CONTRACT]);

        }

        //force this blockchain into contract
        $return[self::DISPLAY_CONTRACT]['blockchain']= $blockchain ;

        $return[self::DISPLAY_ADAPTED_QUANTITY] = NULL ;
        //does it have adapted quantity ?
        if ($this->getBlockchainContract()->decimals){
            $quantity = $this->get(BlockchainEventFactory::EVENT_QUANTITY);
            $adaptedQuantity = $quantity ;
            if ($this->getBlockchainContract()->decimals > 0){
                $adaptedQuantity = $quantity / pow(10,$this->getBlockchainContract()->decimals);
            }
            $return[self::DISPLAY_ADAPTED_QUANTITY] = $adaptedQuantity ;
        }

        $return[self::DISPLAY_QUANTITY] = $this->get(BlockchainEventFactory::EVENT_QUANTITY);

        $return[self::DISPLAY_TIMESTAMP] = $this->getBlockTimestamp();
        $return[self::DISPLAY_BLOCK_ID] = $this->getBlock()->getId();

        //autofixer if blocktime doens't exist for block
        if (! $return[self::DISPLAY_TIMESTAMP]){ //blocktime not on block
            if ($this->get(BlockchainEventFactory::EVENT_BLOCK_TIME) > 1){ //legacy blocktime exist
                $block = $this->getBlock();
                $block->setTimestamp($this->get(BlockchainEventFactory::EVENT_BLOCK_TIME));
                $return[self::DISPLAY_TIMESTAMP] = $this->get(BlockchainEventFactory::EVENT_BLOCK_TIME);
            }

        }


        $return[self::DISPLAY_TIMESTAMP_LEGACY] = $this->get(BlockchainEventFactory::EVENT_BLOCK_TIME);


        $contract =  $this->getBlockchainContract();
        $collections = $contract->getCollections();


        //here we are building to much factories
        if(is_array($collections) &&  $this->displayManager->params['withOrbs']) {
            $orbFactory = new OrbFactory();
            $orbArray = $orbFactory->getOrbFromSpecifier($this->getSpecifier(), $contract, reset($collections));

            foreach ($orbArray ? $orbArray : array() as $orb) {
                /**@var Orb $orb */
                $orbArray = $orb->getAsset()->display()->return();
                $orbArray['asset'] = $orb->getAsset()->display()->return();
                $orbArray['imageUrl'] = $orb->getAsset()->imageUrl ;
                $orbArray['collection']['name'] = $orb->assetCollection->name;
                $orbArray['collection']['id'] = $orb->assetCollection->getId();
                $return['orbs'][] = $orbArray ; //legacy support
                // $return['orbs'][] = $orb->getAsset()->display()->return();

            }
        }

        return $return ;
    }

    public function display($withOrbs=true): DisplayManager
    {
        if (!isset($this->displayManager)){
            $this->displayManager = new DisplayManager($this);
        }
        $this->displayManager->params['withOrbs'] = $withOrbs ;

        return $this->displayManager ;
    }
}
