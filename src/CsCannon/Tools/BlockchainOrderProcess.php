<?php


namespace CsCannon\Tools;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Blockchains\Substrate\Kusama\KusamaBlockchain;
use Exception;
use phpDocumentor\Reflection\Types\Boolean;
use SandraCore\Entity;

class BlockchainOrderProcess
{

    private $needleContractId;
    public $blockchain;

    public function __construct(Blockchain $blockchain)
    {
        $this->blockchain = $blockchain;
    }




    /**
     * @return BlockchainOrder[]
     */
    public function getAllMatches()
    {
        $factory = new BlockchainOrderFactory($this->blockchain);
        $orders = $factory->getAllEntitiesOnChain();

        $matches = [];

        foreach ($orders as $order) {

            $isClose = $order->getReference(BlockchainOrderFactory::STATUS);

            if(!is_null($isClose) && $isClose->refValue == BlockchainOrderFactory::CLOSE){
                return $matches;
            }

            $contractToBuyId = $order->getContractToBuy()->getReference('id')->refValue;
            $matchWithBuy = false;
            $matchOrders = [];

            if (!is_null($contractToBuyId)){

                $this->needleContractId = $contractToBuyId;
                $matchOrders = array_filter($orders, [$this, 'filterBySellContract']);
                $matchWithBuy = !empty($matchOrders);

                if(!$matchWithBuy){
                    $contractToSellId = $order->getContractToSell()->getReference('id')->refValue;
                    if(!is_null($contractToSellId)){
                        $this->needleContractId = $contractToSellId;
                        $matchOrders = array_filter($orders, [$this, 'filterBySellContract']);
                    }
                }
            }

            if(!empty($matchOrders)){
                $matches = $this->matchMaker($order, $matchOrders, $matchWithBuy);
            }
        }

    }



    public function makeMatchOneByOne()
    {
        return true;
    }



    /**
     * @param BlockchainOrder[] $orders
     * @param BlockchainOrder $orderToMatch
     * @param bool $matchWithBuy
     * @return array
     */
    private function matchMaker(array $orders, BlockchainOrder $orderToMatch, bool $matchWithBuy)
    {

        foreach ($orders as $order){

            // orders sell's contracts match with $orderToMatch->getContractToBuy()
            if($matchWithBuy){
                $orderToMatchQuantity = $orderToMatch->getContractToBuyQuantity();
                $orderQuantity = $order->getContractToSellQuantity();

                $orderToMatchTradeQuantity = $orderToMatch->getContractToSellQuantity();
                $orderTradeQuantity = $order->getContractToBuyQuantity();

            }else{
                $orderToMatchQuantity = $orderToMatch->getContractToSellQuantity();
                $orderQuantity = $order->getContractToBuyQuantity();

                $orderToMatchTradeQuantity = $orderToMatch->getContractToBuyQuantity();
                $orderTradeQuantity = $order->getContractToSellQuantity();
            }

            // TODO calcul quantities match and remaining
//            if($orderToMatchQuantity >= $orderQuantity && )
            return [];
        }

    }


    /**
     * @param BlockchainOrder $sellOrder
     * @param BlockchainOrder $buyOrder
     * @return bool
     * @throws Exception
     */
    protected function sendMatchAndUpdate(BlockchainOrder $sellOrder, BlockchainOrder $buyOrder)
    {
        try{
            $initialBuyQuantity = $sellOrder->getContractToBuyQuantity();
            $initialSellQuantity = $sellOrder->getContractToSellQuantity();

            $matchRemainingBuy = $sellOrder->getContractToBuyQuantity() - $buyOrder->getContractToSellQuantity();
            $matchRemainingSell = $sellOrder->getContractToSellQuantity() - $buyOrder->getContractToBuyQuantity();

            $sellOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_BUY, $matchRemainingBuy);
            $sellOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_SELL, $matchRemainingSell);
            $sellOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_TOTAL, $matchRemainingBuy * $matchRemainingSell);

            $remainingBuy = $buyOrder->getContractToBuyQuantity() - $initialSellQuantity;
            $remainingSell = $buyOrder->getContractToSellQuantity() - $initialBuyQuantity;

            $buyOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_BUY, $remainingBuy);
            $buyOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_SELL, $remainingSell);
            $buyOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_TOTAL, $remainingBuy * $remainingSell);

            if($sellOrder->getTotal() == '0'){
                $sellOrder->closeOrder();
            }
            if($buyOrder->getTotal() == '0'){
                $buyOrder->closeOrder();
            }

            $matchQuantity[BlockchainOrderFactory::MATCH_BUY_QUANTITY] = $initialBuyQuantity;
            $matchQuantity[BlockchainOrderFactory::MATCH_SELL_QUANTITY] = $initialSellQuantity;

            $sellOrder->setBrotherEntity(BlockchainOrderFactory::MATCH_WITH, $buyOrder, $matchQuantity);

            try{
                BlockchainOrderFactory::makeEventFromMatches($sellOrder, $buyOrder);
            }catch(Exception $e){
                throw $e;
            }

            return true;

        }catch(Exception $e){
            throw $e;
        }

    }



    /**
     * @param BlockchainOrder[] $orders
     * @param bool $withMatch
     * @return array
     */
    public function makeViewFromOrders(array $orders, Boolean $withMatch)
    {
        $response = [];
        $matchArray = [];
        foreach ($orders as $order){

            $contractToBuyId = $order->getContractToBuy()->getReference('id')->refValue;
            $contractToSellId = $order->getContractToSell()->getReference('id')->refValue;
            $orderStatus = $order->getReference(BlockchainOrderFactory::STATUS);

            $matchArray['source'] = $order->getSource()->getAddress();
            $matchArray['contract_buy'] = $contractToBuyId;
            $matchArray['remaining_buy_quantity'] = $order->getContractToBuyQuantity();
            $matchArray['contract_sell'] = $contractToSellId;
            $matchArray['remaining_sell_quantity'] = $order->getContractToSellQuantity();
            $matchArray['remaining_total'] = $order->getTotal();
            $matchArray['block'] = $order->getBlock()->getId();
            $matchArray['txid'] = $order->getTxId();
            $matchArray['status'] = is_null($orderStatus) ? "open" : $orderStatus->refValue;

            if($order->getBlockchain()::NAME === KusamaBlockchain::NAME){
                $matchArray['order_type'] = $order->getBuyDestination() ? 'BUY' : 'LIST';
            }

            /** @var Entity[] $brothers */
            $brothers = $order->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
            /** @var Entity[] $matchedOrders */
            $matchedOrders = $order->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);

            $matchWith = [];


            if($withMatch && $matchedOrders && $brothers){

                for($i = 0; $i<count($matchedOrders); $i++){

                    /** @var BlockchainOrder $matchedOrder */
                    $matchedOrder = $matchedOrders[$i];

                    $brothersKeys = array_keys($brothers);
                    $brother = $brothers[$brothersKeys[$i]];

                    $source = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS);
                    /** @var BlockchainAddress $sourceAddress */
                    $sourceAddress = end($source);
                    $matchWith[$i]['source'] = $sourceAddress->getAddress();

                    $contracts = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::ORDER_BUY_CONTRACT);
                    /** @var BlockchainContract $contract */
                    $contract = end($contracts);
                    $contractId = $contract->getReference('id')->refValue ?? null;
                    $matchWith[$i]['contract_buy'] = $contractId;

                    $quantityBuyMatch = $brother->getReference(BlockchainOrderFactory::MATCH_BUY_QUANTITY)->refValue ?? null;
                    $matchWith[$i]['match_buy_quantity'] = $quantityBuyMatch;


                    $tokensBuy = $matchedOrder->getBrotherEntity(BlockchainOrderFactory::TOKEN_BUY) ?? null;
                    if(!is_null($tokensBuy)){
                        $tokenBuy = $matchedOrder->getTokenBuy()->getDisplayStructure();
                    }else{
                        $tokenBuy = $this->blockchain->getMainCurrencyTicker();
                    }

                    $matchWith[$i]['token_buy'] = $tokenBuy;


                    $contractsSell = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::ORDER_SELL_CONTRACT);
                    /** @var BlockchainContract $contractSell */
                    $contractSell = end($contractsSell);
                    $contractSellId = $contractSell->getReference('id')->refValue ?? null;
                    $matchWith[$i]['contract_sell'] = $contractSellId;

                    $quantitySellMatch = $brother->getReference(BlockchainOrderFactory::MATCH_SELL_QUANTITY)->refValue ?? null;
                    $matchWith[$i]['match_sell_quantity'] = $quantitySellMatch;


                    $tokensSell = $matchedOrder->getBrotherEntity(BlockchainOrderFactory::TOKEN_SELL) ?? null;
                    if(!is_null($tokensSell)){
                        $tokenSell = $matchedOrder->getTokenSell()->getDisplayStructure();
                    }else{
                        $tokenSell = $this->blockchain->getMainCurrencyTicker();
                    }

                    $matchWith[$i]['token_sell'] = $tokenSell;


                    $sellRemainingQuantity = $matchedOrder->getReference(BlockchainOrderFactory::REMAINING_SELL)->refValue ?? null;
                    if(is_null($sellRemainingQuantity)) $sellRemainingQuantity = $matchedOrder->getReference(BlockchainOrderFactory::SELL_PRICE)->refValue ?? null;
                    $matchWith[$i]['remaining_to_sell'] = $sellRemainingQuantity;

                    $buyRemainingQuantity = $matchedOrder->getReference(BlockchainOrderFactory::REMAINING_BUY)->refValue ?? null;
                    if(is_null($sellRemainingQuantity)) $buyRemainingQuantity = $matchedOrder->getReference(BlockchainOrderFactory::BUY_TOTAL)->refValue ?? null;
                    $matchWith[$i]['remaining_to_buy'] = $buyRemainingQuantity;


                    $blocks = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::EVENT_BLOCK);
                    /** @var BlockchainBlock $block */
                    $block = end($blocks);
                    $matchWith[$i]['block'] = $block->getId();

                    $isClose = $order->getReference(BlockchainOrderFactory::STATUS)->refValue;
                    if(!is_null($isClose) || $isClose == BlockchainOrderFactory::CLOSE){
                        $matchWith[$i][BlockchainOrderFactory::STATUS] = $isClose;
                    }

                }
                $matchArray['match_with'] = $matchWith;
            }
            $response[] = $matchArray;
        }

        return $response;
    }


    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    private function filterBySellContract(BlockchainOrder $order)
    {
        $contractToSellId = $order->getContractToSell()->getReference('id')->refValue;
        return !is_null($contractToSellId) && $this->needleContractId === $contractToSellId;
    }



    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    private function filterByBuyContract(BlockchainOrder $order)
    {
        $contractToSellId = $order->getContractToBuy()->getReference('id')->refValue;
        return !is_null($contractToSellId) && $this->needleContractId === $contractToSellId;
    }


}
