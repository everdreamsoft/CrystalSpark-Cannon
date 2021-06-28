<?php

namespace CsCannon\Blockchains;

use App\Models\BlockchainOrder;
use App\Models\KusamaCryptoContract;
use App\Services\BlockchainOrderService;
use CsCannon\BlockchainRouting;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory;
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

    public static $isa = "BlockchainOrder";
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

    // TODO triplet
    const STATUS = "status";
    const CLOSE = "close";

    const TOKEN_BUY = "tokenBuy";
    const TOKEN_SELL = "tokenSell";


    public function __construct(Blockchain $blockchain)
    {

        $this->blockchain = $blockchain  ;
        return parent::__construct();
    }


    /**
     * @param Blockchain $blockchain
     * @param bool $asSandraEntity
     * @param string $filter
     * @return Entity[]|BlockchainOrder[]
     */
    private static function getAllEntities(Blockchain $blockchain, bool $asSandraEntity = true, $filter = ""): array
    {
        $orders = new BlockchainOrderFactory();
        $populate = self::populateAll($blockchain, $orders, $filter);

        $allOrders = $populate->getEntities();

        if($asSandraEntity){
            return $allOrders;
        }

        $blockchainOrders = [];
        foreach ($allOrders as $order){
            $blockchainOrders[] = new BlockchainOrder($order);
        }

        return $blockchainOrders;
    }

    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC', $sortByRef = null, $numberSort = false)
    {
        $populated =  parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        $blockchain = $this->blockchain ;

        $this->populateBrotherEntities(BlockchainOrderFactory::MATCH_WITH);


        $this->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, $blockchain->getAddressFactory());
        $this->joinFactory(BlockchainOrderFactory::TOKEN_BUY, new BlockchainStandardFactory($this->system));
        $this->joinFactory(BlockchainOrderFactory::TOKEN_SELL, new BlockchainStandardFactory($this->system));
        $this->joinFactory(BlockchainOrderFactory::EVENT_BLOCK, $blockchain->getBlockFactory());
        $this->joinFactory(BlockchainOrderFactory::ORDER_BUY_CONTRACT, $blockchain->getContractFactory());
        $this->joinFactory(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $blockchain->getContractFactory());
        $this->joinFactory(BlockchainOrderFactory::BUY_DESTINATION, $blockchain->getAddressFactory());
        $this->joinPopulate();

        return $populated ;

    }

    public function matchPopulate(){

        $matchOrderFactory = new BlockchainOrderFactory();
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, clone $blockchain->getAddressFactory());
        $matchOrderFactory->joinPopulate();

    }


    /**
     * @param Blockchain $blockchain
     * @param BlockchainOrderFactory $factory
     * @param string $filter
     * @return BlockchainOrderFactory
     */
    private static function populateAll(Blockchain $blockchain, BlockchainOrderFactory $factory, $filter = ""): BlockchainOrderFactory
    {
        /** @var $sandra System */
        $sandra = App('Sandra')->getSandra();

//        if($filter != ""){
//            $factory->setFilter($filter);
//        }

        // TODO get variable, not strings
//        $factory->setFilter(BlockchainOrderFactory::MATCH_WITH);

        $matchOrderFactory = new BlockchainOrderFactory();
        $matchOrderFactory->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, clone $blockchain->getAddressFactory());

        $factory->populateLocal();
        $factory->populateBrotherEntities(BlockchainOrderFactory::MATCH_WITH);
        $factory->joinFactory(BlockchainOrderFactory::MATCH_WITH, $matchOrderFactory);
        $factory->joinFactory(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, $blockchain->getAddressFactory());
        $factory->joinFactory(BlockchainOrderFactory::TOKEN_BUY, new BlockchainStandardFactory($sandra));
        $factory->joinFactory(BlockchainOrderFactory::TOKEN_SELL, new BlockchainStandardFactory($sandra));
        $factory->joinFactory(BlockchainOrderFactory::EVENT_BLOCK, $blockchain->getBlockFactory());
        $factory->joinFactory(BlockchainOrderFactory::ORDER_BUY_CONTRACT, $blockchain->getContractFactory());
        $factory->joinFactory(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $blockchain->getContractFactory());
        $factory->joinFactory(BlockchainOrderFactory::BUY_DESTINATION, $blockchain->getAddressFactory());
//        self::populateAll($blockchain, $matchOrderFactory);
        $factory->joinPopulate();

        $matchOrderFactory->joinPopulate();

        return $factory;
    }


    /**
     * @param Entity $order
     * @param System|null $sandra
     * @return Blockchain|null
     */
    public static function getBlockchainFromOrder(Entity $order, System $sandra = null): ?Blockchain
    {

        $conceptTriplets = $order->subjectConcept->getConceptTriplets();
        $conceptId = $conceptTriplets[$sandra->systemConcept->get(BlockchainOrderFactory::ON_BLOCKCHAIN)] ?? null;
        $lastId = end($conceptId);
        $blockchainName = $sandra->systemConcept->getSCS($lastId);

        return BlockchainRouting::getBlockchainFromName($blockchainName);
    }


    /**
     * @param Blockchain $blockchain
     * @param bool $needMatchedOrders
     * @return array
     */
    public static function getMatchedOrUnmatched(Blockchain $blockchain, bool $needMatchedOrders): array
    {

        $allOrders = self::getAllEntities($blockchain, false);
        $ordersOnChain = array_filter($allOrders, [new BlockchainOrderService($blockchain), 'filterSameChain']);

        $ordersForView = [];

        foreach ($ordersOnChain as $order){

            $matchEntity = $order->getOrder()->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);

            if($needMatchedOrders && $matchEntity){
                $ordersForView[] = $order;
            }else if(!$needMatchedOrders && !$matchEntity){
                $ordersForView[] = $order;
            }
        }

        $orderService = new BlockchainOrderService($blockchain);
        return $orderService->makeViewFromOrders($ordersForView, $blockchain, $needMatchedOrders);
    }



    /**
     * @param Blockchain $blockchain
     * @return array
     */
    public static function viewAllOrdersOnChain(Blockchain $blockchain): array
    {
        $allOrders = self::getAllEntities($blockchain, false);

        $ordersOnChain = array_filter($allOrders, [new BlockchainOrderService($blockchain), 'filterSameChain']);

        $chainOrders = [];

        foreach ($ordersOnChain as $order){

            $chain['source'] = $order->getSource()->getAddress();

            $chain['order_type'] = $order->getBuyDestination() ? 'BUY' : 'LIST';

            $chain['to_sell']['contract'] = $order->getContractToSellId();
            $chain['to_sell']['quantity'] = $order->getContractToSellQuantity();

            $chain['to_buy']['contract'] = $order->getContractToBuyId();
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

        // TODO make abstract MainChainToken for cryptos in CsCannon
        // and replace != "KSM" by instanceof MainChainToken

        $needleBuyContractId = $needleOrder->getContractToBuyId();
        $needleSellContractId = $needleOrder->getContractToSellId();


        if(strtoupper($needleBuyContractId) != "KSM"){

            try{
                $eventFactory->create(
                    $blockchain,
                    $needleOrder->getSource(),
                    $matchOrder->getSource(),
                    $needleOrder->getContractToBuy(),
                    $needleOrder->getTxId(),
                    $needleOrder->getBlock()->getTimestamp(),
                    $needleOrder->getBlock(),
                    $needleOrder->getTokenBuy(),
                    $needleOrder->getContractToBuyQuantity()
                );

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

        if(strtoupper($needleSellContractId) != "KSM"){

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

                $eventFactory->create(
                    $blockchain,
                    $matchOrder->getSource(),
                    $needleOrder->getSource(),
                    $matchOrder->getContractToBuy(),
                    $matchOrder->getTxId(),
                    $matchOrder->getBlock()->getTimestamp(),
                    $matchOrder->getBlock(),
                    $matchOrder->getTokenBuy(),
                    $matchOrder->getContractToBuyQuantity()
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
    public static function deleteAll(Blockchain $blockchain): array
    {

        $entities = self::getAllEntities($blockchain);

        if(count($entities) == 0){
            return $entities;
        }

        foreach ($entities as $entity){
            $entity->delete();
        }

        return $entities;
    }


    /**
     * @param Blockchain $blockchain
     * @return array
     */
    public static function makeMatchOrders(Blockchain $blockchain): array
    {

        $allOrders = self::getAllEntities($blockchain, false);

        $orderService = new BlockchainOrderService($blockchain);
        $matches = $orderService->getMatchesOrders($allOrders);
        /** @var BlockchainOrder[] $matches */

        $matches = self::getAllEntities($blockchain, false);

        $response = $orderService->makeViewFromOrders($matches, $blockchain, true);


        return $response;

    }




}
