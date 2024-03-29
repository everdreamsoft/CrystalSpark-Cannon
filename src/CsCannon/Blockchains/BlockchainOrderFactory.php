<?php

namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Generic\GenericBlockchain;
use CsCannon\Blockchains\Generic\GenericContract;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Blockchains\Substrate\Kusama\KusamaAddress;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use CsCannon\BlockchainStandardFactory;
use CsCannon\CSEntityFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\CommonFunctions;
use SandraCore\DatabaseAdapter;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;


/**
 * Class BlockchainOrderFactory
 * @package CsCannon\Blockchains
 *
 *
 *
 *   @method BlockchainOrderFactory             createNew($dataArray, $linArray = null) : BlockchainOrder            Use the method create instead unless you know what you are doing
 *
 */
class BlockchainOrderFactory extends BlockchainEventFactory
{

    public static $isa = "blockchainOrder";
    public static $file = "blockchainOrderFile";
    protected static  $className = 'CsCannon\Blockchains\BlockchainOrder' ;



    const EVENT_SOURCE_ADDRESS = 'source';
    const EVENT_BLOCK_TIME = 'timestamp';
    const ON_BLOCKCHAIN = 'onBlockchain';
    const EVENT_BLOCK = 'onBlock';

    const BUY_AMOUNT = "buyAmount";
    const SELL_PRICE = "sellPrice";
    const BUY_TOTAL = "buyTotal";
    const ORDER_BUY_CONTRACT = "buyContract";
    const ORDER_SELL_CONTRACT = "sellContract";
    const BUY_DESTINATION = "buyDestination";

    const MATCH_WITH = "matchWith";
    const REMAINING_BUY = "remainingBuy";
    const REMAINING_SELL = "remainingSell";
    const REMAINING_TOTAL = "remainingTotal";
    const MATCH_BUY_QUANTITY = "matchBuyQuantity";
    const MATCH_SELL_QUANTITY = "matchSellQuantity";

    const STATUS = "status";
    const CLOSE = "close";
    const NOBALANCE = "insufficientBalance";
    const CANCELLED = "cancelled";

//    const TOKEN_BUY = "tokenBuy";
//    const TOKEN_SELL = "tokenSell";


    public function __construct(Blockchain $blockchain)
    {
        $this->blockchain = $blockchain;
        return parent::__construct();
    }



    public function populateWithMatch()
    {
        $blockchain = $this->blockchain;

        $matchOrderFactory = new BlockchainOrderFactory($blockchain);
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, clone $blockchain->getAddressFactory());
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::TOKEN_BUY, new BlockchainStandardFactory($this->system));
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::TOKEN_SELL, new BlockchainStandardFactory($this->system));
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::EVENT_BLOCK, clone $blockchain->getBlockFactory());
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::ORDER_BUY_CONTRACT, clone $blockchain->getContractFactory());
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::ORDER_SELL_CONTRACT, clone $blockchain->getContractFactory());
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::BUY_DESTINATION, clone $blockchain->getAddressFactory());

        $this->populateBrotherEntities(BlockchainOrderFactory::MATCH_WITH);
        $this->joinFactory(BlockchainOrderFactory::MATCH_WITH, $matchOrderFactory);
        $this->populateLocal();

        $matchOrderFactory->joinPopulate();

        return $this;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $asc
     * @param null $sortByRef
     * @param false $numberSort
     * @return Entity[]
     */
    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC', $sortByRef = null, $numberSort = false): array
    {
        $populated =  parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        $blockchain = $this->blockchain ;

        $this->populateBrotherEntities(BlockchainOrderFactory::MATCH_WITH);
        $this->populateBrotherEntities(BlockchainOrderFactory::STATUS);
        $this->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, $blockchain->getAddressFactory());
        $this->joinFactory(BlockchainOrderFactory::TOKEN_BUY, new BlockchainStandardFactory($this->system));
        $this->joinFactory(BlockchainOrderFactory::TOKEN_SELL, new BlockchainStandardFactory($this->system));
        $this->joinFactory(BlockchainOrderFactory::ORDER_BUY_CONTRACT, $blockchain->getContractFactory());
        $this->joinFactory(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $blockchain->getContractFactory());
        $this->joinFactory(BlockchainOrderFactory::BUY_DESTINATION, $blockchain->getAddressFactory());
        $this->joinPopulate();

        return $populated ;

    }


    /**
     * @return BlockchainOrder|false
     */
    public function getLastBuy()
    {
        $this->setFilter(self::STATUS, 0, true);
        $this->setFilter(self::BUY_DESTINATION);
        $this->populateLocal(1);
        /** @var BlockchainOrder[] $orders */
        $orders = $this->getEntities();

        return end($orders);
    }

    /**
     * @return BlockchainOrder[]
     */
    public function getLastListCancellation(): array
    {
        $this->setFilter(BlockchainOrderFactory::STATUS, 0 , true);
        $this->setFilter(BlockchainOrderFactory::BUY_DESTINATION, 0 , true);
        $cancellations = $this->populateFromSearchResults("0", BlockchainOrderFactory::BUY_AMOUNT);

        if(empty($cancellations)){
            $cancellations = $this->populateFromSearchResults("0", BlockchainOrderFactory::SELL_PRICE);
        }

        return $cancellations;
    }



    /**
     * @param int $limit
     * @param int $offset
     * @return BlockchainOrder[]
     */
    public function getListsOnly(int $limit, int $offset)
    {
        $this->setFilter(self::BUY_DESTINATION, 0, true);
        $this->setFilter(self::STATUS, 0 , true);
        $this->populateLocal($limit, $offset);
        /** @var BlockchainOrder[] $orders */
        $orders = $this->getEntities();
        return $orders;
    }


    /**
     * @return BlockchainOrder[]
     */
    public function getAllEntitiesOnChain()
    {
        $this->populateLocal();
        $allEntities = $this->getEntities();

        return array_filter($allEntities, [$this, 'filterSameChain']);
    }



    /**
     * @param Entity $order
     * @param System|null $sandra
     * @return Blockchain|null
     */
    public function getBlockchainFromOrder(Entity $order, System $sandra = null)
    {
        $conceptTriplets = $order->subjectConcept->getConceptTriplets();
        $conceptId = $conceptTriplets[$sandra->systemConcept->get(BlockchainOrderFactory::ON_BLOCKCHAIN)] ?? null;
        $lastId = end($conceptId);
        $blockchainName = $sandra->systemConcept->getSCS($lastId);

        return BlockchainRouting::getBlockchainFromName($blockchainName);
    }


    /**
     * @return BlockchainOrder[]
     */
    public function getClosedOrders()
    {
        $this->setFilter(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CLOSE);
        $this->populateLocal();
        /** @var BlockchainOrder[] $orders */
        $orders = $this->getEntities();
        return $orders;
    }



    /**
     * @param Blockchain $blockchain
     * @return array
     */
    public function viewAllOrdersOnChain(Blockchain $blockchain)
    {
        $this->populateLocal();
        $allOrders = $this->getEntities();

        $ordersOnChain = array_filter($allOrders, [$this, 'filterSameChain']);

        $chainOrders = [];

        foreach ($ordersOnChain as $order){

            $isClose = $order->getReference(BlockchainOrderFactory::STATUS)->refValue;

            if(!is_null($isClose) || $isClose == BlockchainOrderFactory::CLOSE){
                $chain[BlockchainOrderFactory::STATUS] = $isClose;
            }

            $chain['source'] = $order->getSource()->getAddress();

            if($blockchain::NAME === KusamaBlockchain::NAME){
                $chain['order_type'] = $order->getBuyDestination() ? 'BUY' : 'LIST';
            }

            $chain['to_sell']['contract'] = $order->getContractToSell()->getReference('id')->refValue;
            $chain['to_sell']['quantity'] = $order->getContractToSellQuantity();

            $chain['to_buy']['contract'] = $order->getContractToBuy()->getReference('id')->refValue;
            $chain['to_buy']['quantity'] = $order->getContractToBuyQuantity();
            $chain['to_buy']['destination'] = $order->getBuyDestination() ? $order->getBuyDestination()->getAddress() : null;

            $chain['total'] = $order->getTotal();
            $chain['timestamp'] = $order->getBlock()->getTimestamp();

            $chainOrders[strtoupper($blockchain::NAME)][] = $chain;

        }

        return $chainOrders;
    }


    /**
     * @param BlockchainOrder $sellOrder
     * @param BlockchainOrder $buyOrder
     * @param int $quantitySell
     * @throws Exception
     */
    public static function makeEventFromMatches(BlockchainOrder $sellOrder, BlockchainOrder $buyOrder, int $quantitySell = 1)
    {

        $blockchain = $buyOrder->getBlockchain();
        $eventFactory = $blockchain->getEventFactory();

        $needleBuyContractId = $buyOrder->getContractToBuy()->getReference('id')->refValue;
        $needleSellContractId = $buyOrder->getContractToSell()->getReference('id')->refValue;

        $currency = $buyOrder->getBlockchain()->getMainCurrencyTicker();

        try{
            $sellOrder->getTokenSell()->getDisplayStructure();
            $token = $sellOrder->getTokenSell();
        }catch(Exception $e){
            $token = $sellOrder->getTokenBuy();
        }

        if(strtoupper($needleBuyContractId) != $currency){

            try{
                $eventFactory->create(
                    $blockchain,
                    $sellOrder->getSource(),
                    $buyOrder->getSource(),
                    $sellOrder->getContractToSell(),
                    $sellOrder->getTxId(),
                    $sellOrder->getBlock()->getTimestamp($blockchain::NAME),
                    $sellOrder->getBlock(),
                    $token,
                    $quantitySell
                );

            }catch(Exception $e){
                throw $e;
            }

        }

//        if(strtoupper($needleSellContractId) != $currency){
//
//            try{
//                $eventFactory->create(
//                    $blockchain,
//                    $buyOrder->getSource(),
//                    $sellOrder->getSource(),
//                    $buyOrder->getContractToSell(),
//                    $buyOrder->getTxId(),
//                    $buyOrder->getBlock()->getTimestamp(),
//                    $buyOrder->getBlock(),
//                    $buyOrder->getTokenSell(),
//                    $buyOrder->getContractToSellQuantity()
//                );
//
//            }catch(Exception $e){
//                throw $e;
//            }
//        }

    }



    /**
     * @param Blockchain $blockchain
     * @return Entity[]
     */
    public function deleteAll(Blockchain $blockchain)
    {
        $this->populateLocal();
        $entities = $this->getEntities();

        if(count($entities) == 0){
            return $entities;
        }

        foreach ($entities as $entity){
            $entity->delete();
        }

        return $entities;
    }



    /**
     * @param BlockchainOrder $order
     * @return BlockchainOrder|null
     */
    public function filterSameChain(BlockchainOrder $order)
    {
        return ($order->getBlockchain()::NAME == $this->blockchain::NAME) ? $order : null;
    }



}
