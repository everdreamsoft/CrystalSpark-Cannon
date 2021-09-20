<?php

namespace CsCannon\Blockchains;

use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use CsCannon\BlockchainStandardFactory;
use CsCannon\CSEntityFactory;
use Exception;
use SandraCore\Entity;
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

//    const TOKEN_BUY = "tokenBuy";
//    const TOKEN_SELL = "tokenSell";


    public function __construct(Blockchain $blockchain)
    {
        $this->blockchain = $blockchain;
        return parent::__construct();
    }


    /**
     * @return BlockchainOrder[]
     */
    public function getAllEntitiesOnChain(): array
    {
        // TODO: filter in populate for chain
        $this->populateLocal();
        $allEntities = $this->getEntities();

        return array_filter($allEntities, [$this, 'filterSameChain']);
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
     * @param Entity $order
     * @param System|null $sandra
     * @return Blockchain|null
     */
    public function getBlockchainFromOrder(Entity $order, System $sandra = null): ?Blockchain
    {
        $conceptTriplets = $order->subjectConcept->getConceptTriplets();
        $conceptId = $conceptTriplets[$sandra->systemConcept->get(BlockchainOrderFactory::ON_BLOCKCHAIN)] ?? null;
        $lastId = end($conceptId);
        $blockchainName = $sandra->systemConcept->getSCS($lastId);

        return BlockchainRouting::getBlockchainFromName($blockchainName);
    }


//    /**
//     * @param Blockchain $blockchain
//     * @param bool $needMatchedOrders
//     * @return array
//     */
//    public function getMatchedOrUnmatched(Blockchain $blockchain, bool $needMatchedOrders): array
//    {
//        $this->populateLocal();
//        $allOrders = $this->getEntities();
//        $ordersOnChain = array_filter($allOrders, [$this, 'filterSameChain']);
//
//        $ordersForView = [];
//
//        foreach ($ordersOnChain as $order){
//
//            $matchEntity = $order->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
//
//            if($needMatchedOrders && $matchEntity){
//                $ordersForView[] = $order;
//            }else if(!$needMatchedOrders && !$matchEntity){
//                $ordersForView[] = $order;
//            }
//
//        }
//
//        $orderProcess = $this->blockchain->getOrderFactory();
//        return $orderProcess->makeViewFromOrders($ordersForView, $needMatchedOrders);
//    }



    /**
     * @param Blockchain $blockchain
     * @return array
     */
    public function viewAllOrdersOnChain(Blockchain $blockchain): array
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
     * @param BlockchainOrder $matchOrder
     * @param BlockchainOrder $needleOrder
     * @throws Exception
     */
    public static function makeEventFromMatches(BlockchainOrder $matchOrder, BlockchainOrder $needleOrder)
    {

        $blockchain = $needleOrder->getBlockchain();
        $eventFactory = $blockchain->getEventFactory();

        $needleBuyContractId = $needleOrder->getContractToBuy()->getReference('id')->refValue;
        $needleSellContractId = $needleOrder->getContractToSell()->getReference('id')->refValue;

        $currency = $needleOrder->getBlockchain()->getMainCurrencyTicker();

        if(strtoupper($needleBuyContractId) != $currency){

            try{
                $eventFactory->create(
                    $blockchain,
                    $matchOrder->getSource(),
                    $needleOrder->getSource(),
                    $matchOrder->getContractToSell(),
                    $matchOrder->getTxId(),
                    $matchOrder->getBlock()->getTimestamp(),
                    $matchOrder->getBlock(),
                    $matchOrder->getTokenSell(),
                    $matchOrder->getContractToSellQuantity()
                );

            }catch(Exception $e){
                throw $e;
            }

        }

        if(strtoupper($needleSellContractId) != $currency){

            try{
                $eventFactory->create(
                    $blockchain,
                    $needleOrder->getSource(),
                    $matchOrder->getSource(),
                    $needleOrder->getContractToSell(),
                    $needleOrder->getTxId(),
                    $needleOrder->getBlock()->getTimestamp(),
                    $needleOrder->getBlock(),
                    $needleOrder->getTokenSell(),
                    $needleOrder->getContractToSellQuantity()
                );

            }catch(Exception $e){
                throw $e;
            }
        }

    }



    /**
     * @param Blockchain $blockchain
     * @return Entity[]
     */
    public function deleteAll(Blockchain $blockchain): array
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


//    /**
//     * @param Blockchain $blockchain
//     * @return array
//     */
//    public function makeMatchOrders(Blockchain $blockchain): array
//    {
//
//        $allOrders = $this->getAllEntities();
//
//
//        $orderService = new BlockchainOrderService($blockchain);
//        $matches = $orderService->getMatchesOrders($allOrders);
//        /** @var BlockchainOrder[] $matches */
//
//        $matches = $this->getAllEntities();
//
//        $response = $orderService->makeViewFromOrders($matches, $blockchain, true);
//
//
//        return $response;
//
//    }



    /**
     * @param BlockchainOrder $order
     * @return BlockchainOrder|null
     */
    public function filterSameChain(BlockchainOrder $order): ?BlockchainOrder
    {
        return ($order->getBlockchain()::NAME == $this->blockchain::NAME) ? $order : null;
    }


}
