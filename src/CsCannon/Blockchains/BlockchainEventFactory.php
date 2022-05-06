<?php

namespace CsCannon\Blockchains;

use CsCannon\Blockchains\Generic\GenericAddressFactory;
use CsCannon\Blockchains\Generic\GenericBlockchain;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Displayable;
use CsCannon\DisplayManager;
use CsCannon\SandraManager;
use Illuminate\Notifications\Notifiable;
use phpDocumentor\Reflection\Types\Self_;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

class BlockchainEventFactory extends EntityFactory implements Displayable
{
    public $blockchain;
    public static $isa = 'blockchainEvent';
    public static $file = 'blockchainEventFile';

    protected static $className = 'CsCannon\Blockchains\BlockchainEvent';

    public $requirementAbstractArray = array();
    public $requirementAbstractTripletArray = array();
    const EVENT_TYPE = 'eventType';
    const EVENT_SOURCE_ADDRESS = 'source';
    const EVENT_DESTINATION_VERB = 'hasSingleDestination';
    const EVENT_CONTRACT = 'blockchainContract';

    const TOKEN_BUY = 'tokenBuy';
    const TOKEN_SELL = 'tokenSell';


    const BUY_AMOUNT = "buyAmount";
    const SELL_PRICE = "sellPrice";
    const BUY_TOTAL = "buyTotal";
    const ORDER_BUY_CONTRACT = "buyContract";
    const ORDER_SELL_CONTRACT = "sellContract";
    const BUY_DESTINATION = "buyDestination";
    const MATCH_WITH = "matchWith";
    const REMAINING_BUY = "remainingBuy";
    const REMAINING_SELL = "remainingSell";

    const ON_BLOCKCHAIN_EVENT = 'onBlockchain';
    const EVENT_DESTINATION_SIMPLE_VERB = 'destinationAddress';
    const EVENT_QUANTITY = 'quantity';
    const EVENT_BLOCK = 'onBlock';
    // const EVENT_BLOCK_TIME = 'blocktime';
    const EVENT_BLOCK_TIME = 'timestamp';
    public static $messagePool = array();

    public $addressFactory/** @var GenericAddressFactory $addressFactory */
    ;
    public $contractFactory/** @var GenericContractFactory $contractFactory */
    ;


    private $genericAddressFactory = EntityFactory::class;

    const EVENT_TRANSFER = 'transfer';
    const EVENT_ORDER = 'order';

    public $contractAddress = '';

    public $displayManager;


    public function __construct()
    {

        parent::__construct(static::$isa, static::$file, SandraManager::getSandra());


        $this->generatedEntityClass = static::$className;


    }


    public function getRequired()
    {

        $this->requirementAbstractArray = array(Blockchain::$blockchainConceptName,
            self::EVENT_BLOCK_TIME,
            Blockchain::$txidConceptName,

        );

        return $this->requirementAbstractArray;

    }

    public function filterBySender($senderEntity)
    {

        $this->setFilter(BlockchainEventFactory::EVENT_SOURCE_ADDRESS, $senderEntity);

        return $this;

    }

    public function filterByReceiver(BlockchainAddress $receiverEntity)
    {

        $this->setFilter(BlockchainEventFactory::EVENT_SOURCE_ADDRESS, $receiverEntity);

        return $this;

    }

    public function filterByContract(BlockchainContract $contract)
    {

        $this->setFilter(BlockchainEventFactory::EVENT_CONTRACT, $contract);

        return $this;

    }


    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC', $sortByRef = null, $numberSort = false)
    {

        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);

        $blockFactory = new BlockchainBlockFactory(GenericBlockchain::getStatic());
        $this->joinFactory(self::EVENT_BLOCK, $blockFactory);
        $this->joinPopulate();
        $this->populateBrotherEntities();


        $this->getTriplets();


        return $return;
    }

    public function getRequiredTriplets()
    {

        $this->requirementAbstractTripletArray = array(
            self::EVENT_SOURCE_ADDRESS,
            self::EVENT_DESTINATION_VERB,
            self::EVENT_CONTRACT,
            self::EVENT_TYPE,
        );

        return $this->requirementAbstractTripletArray;

    }

    public function create(Blockchain                 $blockchain,
                           BlockchainAddress          $sourceAddressConcept,
                           BlockchainAddress          $destinationAddressConcept,
                           BlockchainContract         $contract,
                                                      $txid,
                                                      $timestamp,
                           BlockchainBlock            $block,
                           BlockchainContractStandard $token = null,
                                                      $quantity = 1,
                                                      $autocommit = true

    )
    {

        $dataArray[Blockchain::$txidConceptName] = $txid;
        $dataArray[self::EVENT_QUANTITY] = $quantity;
        $dataArray[self::EVENT_BLOCK_TIME] = $timestamp;

        /** @var BlockchainContractFactory $contractFactory */

        $triplets[self::EVENT_SOURCE_ADDRESS] = $sourceAddressConcept;
        $triplets[self::EVENT_DESTINATION_VERB] = $destinationAddressConcept;

        $triplets[self::ON_BLOCKCHAIN_EVENT] = $blockchain::NAME;
        $triplets[self::EVENT_BLOCK] = $block;

        //does the contract has a token id ?
        if (!is_null($token)) {
            $stucture = $token->getSpecifierData();


            $triplets[self::EVENT_CONTRACT] = array($contract->subjectConcept->idConcept => $stucture);

        } else {
            $triplets[self::EVENT_CONTRACT] = $contract;

        }


        return parent::createNew($dataArray, $triplets, $autocommit);
    }

    /**
     * @param Blockchain $blockchain
     * @param BlockchainAddress $sourceAddressConcept
     * @param BlockchainContract $buyContract
     * @param BlockchainContract $sellContract
     * @param $buyAmount
     * @param $sellPrice
     * @param $buyTotal
     * @param $txid
     * @param $timestamp
     * @param BlockchainBlock $block
     * @param BlockchainContractStandard|null $tokenBuy
     * @param BlockchainContractStandard|null $tokenSell
     * @param BlockchainAddress|null $buyDestination
     * @return BlockchainOrder
     */
    public function createOrder(Blockchain                 $blockchain,
                                BlockchainAddress          $sourceAddressConcept,
                                BlockchainContract         $buyContract,
                                BlockchainContract         $sellContract,
                                                           $buyAmount,
                                                           $sellPrice,
                                                           $buyTotal,
                                                           $txid,
                                                           $timestamp,
                                BlockchainBlock            $block,
                                BlockchainContractStandard $tokenBuy = null,
                                BlockchainContractStandard $tokenSell = null,
                                BlockchainAddress          $buyDestination = null
    ): BlockchainOrder
    {


        $dataArray[self::BUY_AMOUNT] = $buyAmount;
        $dataArray[self::SELL_PRICE] = $sellPrice;
        $dataArray[self::BUY_TOTAL] = $buyTotal;
        $dataArray[Blockchain::$txidConceptName] = $txid;
        $dataArray[self::EVENT_BLOCK_TIME] = $timestamp;


        /** @var BlockchainContractFactory $contractFactory */

        $triplets[self::EVENT_SOURCE_ADDRESS] = $sourceAddressConcept;
        $triplets[self::EVENT_TYPE] = self::EVENT_ORDER;


        $triplets[self::ORDER_BUY_CONTRACT] = $buyContract;
        $triplets[self::ORDER_SELL_CONTRACT] = $sellContract;

        $triplets[self::ON_BLOCKCHAIN_EVENT] = $blockchain::NAME;
        $triplets[self::EVENT_BLOCK] = $block;

        //buy desitnation = stric address match order
        if ($buyDestination) {

            $triplets[self::BUY_DESTINATION] = $buyDestination;
        }

        //does the contract has a token id ?
        if (!is_null($tokenBuy)) {
            $structure = $tokenBuy->getSpecifierData();
            $triplets[self::TOKEN_BUY] = array($tokenBuy->subjectConcept->idConcept => $structure);

        }
        //does the contract has a token id ?
        if (!is_null($tokenSell)) {
            $structure = $tokenSell->getSpecifierData();
            $triplets[self::TOKEN_SELL] = array($tokenSell->subjectConcept->idConcept => $structure);

        }


        return parent::createNew($dataArray, $triplets);

    }


    public function localVerifyIntegrity($dataArray, $linkArray)
    {

        $sandra = SandraManager::getSandra();
        /** @var System $sandra */


        //First we check if all required parameters are present (for all events)
        foreach ($this->getRequired() as $required) {

            //test if ref exists
            if (!isset($dataArray[$required]) and !isset($dataArray[$sandra->systemConcept->get($required)])) {

                self::$messagePool[] = "$required not defined for transaction";
                return false;
            }


        }
        //if it is a transfer we require a source and a destination

        foreach ($this->getRequiredTriplets() as $requiredVerb) {

            //test if ref exists
            if (!isset($linkArray[$requiredVerb]) and !isset($dataArray[$sandra->systemConcept->get($requiredVerb)])) {

                self::$messagePool[] = "$requiredVerb not defined for transaction";
                return false;
            }


        }

        //we make a check for specific events
        if ($linkArray[BlockchainEventFactory::EVENT_TYPE] == BlockchainEventFactory::EVENT_TRANSFER) {
            if (!$this->verifyTransfer($dataArray, $linkArray)) return false;


        }


        return true;


    }

    private function verifyTransfer($dataArray, $linkArray)
    {


        if ($linkArray[BlockchainEventFactory::EVENT_SOURCE_ADDRESS] == $linkArray[BlockchainEventFactory::EVENT_DESTINATION_VERB]) {

            //dd("we have a birth");
            return false;
        }

        return true;

    }


    public function buildAddressFactory()
    {

        foreach ($this->entityArray as $eventEntity) {

            /** @var BlockchainEvent $eventEntity */

            $source = $eventEntity->getSourceAddress();

        }


    }

    public function returnArray($displayManager)
    {

        $output = array();

        foreach ($this->entityArray ? $this->entityArray : array() as $eventEntity) {

            $contractAdress = null;


            /** @var BlockchainEvent $eventEntity */
            $output[] = $eventEntity->display($this->displayManager->params['withOrbs'] ?? null)->return();


        }

        return $output;


    }

    public function display($withOrbs = true): DisplayManager
    {

        if (!isset($this->displayManager)) {
            $this->displayManager = new DisplayManager($this);
        }
        $this->displayManager->params['withOrbs'] = $withOrbs;

        return $this->displayManager;


    }

    public function populateFromParent($limit = 1000, $offset = 0, $asc = 'DESC', $sortByRef = null, $numberSort = false)
    {
        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
    }


}

