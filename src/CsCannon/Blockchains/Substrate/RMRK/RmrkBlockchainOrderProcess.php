<?php


namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainBlock;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\Interfaces\RmrkContractStandard;
use CsCannon\Tools\BlockchainOrderProcess;
use Exception;
use SandraCore\Entity;


class RmrkBlockchainOrderProcess extends BlockchainOrderProcess
{

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct($blockchain);
    }

    /**
     * @param BlockchainOrder[] $orders
     * @param BlockchainOrder $needleMatch
     * @return array|string
     */
    public function getMatches(array $orders, BlockchainOrder $needleMatch)
    {
        $matches = [];

        foreach ($orders as $matchOrder){

            $isClose = $matchOrder->getReference(BlockchainOrderFactory::STATUS)->refValue;

            // check if orders don't have 'close' status
            if(is_null($isClose) || $isClose != BlockchainOrderFactory::CLOSE){

                $needleContractToBuyId = $needleMatch->getContractToBuy()->getReference('id')->refValue;
                $matchContractToBuyId = $matchOrder->getContractToBuy()->getReference('id')->refValue;

                $needleContractToSellId = $needleMatch->getReference('id')->refValue;
                $matchContractToSellId = $needleMatch->getReference('id')->refValue;

                if($needleContractToBuyId === $matchContractToSellId && $needleContractToSellId === $matchContractToBuyId){
                    // check Contract ID

                    if($needleMatch->getContractToSellQuantity() >= $matchOrder->getContractToBuyQuantity() && $matchOrder->getContractToSellQuantity() >= $needleMatch->getContractToBuyQuantity()){
                        // Check quantities

                        try{
                            $this->sendMatchAndUpdate($matchOrder, $needleMatch);
                        }catch(Exception $e){
                            return $e->getMessage();
                        }

                        $matches[] = $matchOrder;
                    }
                }

            }
        }

        return $matches;
    }


    /**
     * @param BlockchainOrder $matchOrder
     * @param BlockchainOrder $needleOrder
     * @return bool
     * @throws Exception
     */
    private function sendMatchAndUpdate(BlockchainOrder $matchOrder, BlockchainOrder $needleOrder): bool
    {
        try{
            $initialBuyQuantity = $matchOrder->getContractToBuyQuantity();
            $initialSellQuantity = $matchOrder->getContractToSellQuantity();

            $matchOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_BUY, $matchOrder->getContractToBuyQuantity() - $needleOrder->getContractToSellQuantity());
            $matchOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_SELL, $matchOrder->getContractToSellQuantity() - $needleOrder->getContractToBuyQuantity());
            $matchOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_TOTAL, $matchOrder->getContractToBuyQuantity() * $matchOrder->getContractToSellQuantity());

            $needleOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_BUY, $needleOrder->getContractToBuyQuantity() - $initialSellQuantity);
            $needleOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_SELL, $needleOrder->getContractToSellQuantity() - $initialBuyQuantity);
            $needleOrder->createOrUpdateRef(BlockchainOrderFactory::REMAINING_TOTAL, $needleOrder->getContractToBuyQuantity() * $needleOrder->getContractToSellQuantity());

            $matchQuantity[BlockchainOrderFactory::MATCH_BUY_QUANTITY] = $initialBuyQuantity;
            $matchQuantity[BlockchainOrderFactory::MATCH_SELL_QUANTITY] = $initialSellQuantity;

            $matchOrder->setBrotherEntity(BlockchainOrderFactory::MATCH_WITH, $needleOrder, $matchQuantity);


            if($matchOrder->getTotal() == '0'){
                $matchOrder->createOrUpdateRef(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CLOSE);
            }

            try{
                BlockchainOrderFactory::makeEventFromMatches($matchOrder, $needleOrder);
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
    public function makeViewFromOrders(array $orders, bool $withMatch): array
    {
        $response = [];
        $matchArray = [];
        foreach ($orders as $order){

            $contractToBuyId = $order->getContractToBuy()->getReference('id')->refValue;

            $matchArray['source'] = $order->getSource()->getAddress();
            $matchArray['contract_buy'] = $contractToBuyId;
            $matchArray['remaining_quantity'] = $order->getContractToBuyQuantity();
            $matchArray['remaining_total'] = $order->getTotal();
            $matchArray['block'] = $order->getBlock()->getId();
            $matchArray['txid'] = $order->getTxId();

            $matchArray['order_type'] = $order->getBuyDestination() ? 'BUY' : 'LIST';

            /** @var Entity[] $brothers */
            $brothers = $order->getBrotherEntity(BlockchainOrderFactory::MATCH_WITH);
            /** @var Entity[] $matchedOrders */
            $matchedOrders = $order->getJoinedEntities(BlockchainOrderFactory::MATCH_WITH);

            $matchWith = [];

            if($withMatch && $matchedOrders && $brothers){

                for($i = 0; $i<count($matchedOrders); $i++){

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

                    $tokensBuy = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::TOKEN_BUY);
                    /** @var BlockchainContract $tokenBuy */
                    $tokenBuy = end($tokensBuy);

                    if(!($tokenBuy instanceof RmrkContractStandard)){
                        $token = $tokenBuy->getId();
                    }else{
                        $token = $this->blockchain->getMainCurrencyTicker();
                    }
                    $matchWith[$i]['token_buy'] = $token;

                    $contractsSell = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::ORDER_SELL_CONTRACT);
                    /** @var BlockchainContract $contractSell */
                    $contractSell = end($contractsSell);
                    $contractSellId = $contractSell->getReference('id')->refValue ?? null;
                    $matchWith[$i]['contract_sell'] = $contractSellId;

                    $quantitySellMatch = $brother->getReference(BlockchainOrderFactory::MATCH_SELL_QUANTITY)->refValue ?? null;
                    $matchWith[$i]['match_sell_quantity'] = $quantitySellMatch;

                    $tokensSell = $matchedOrder->getJoinedEntities(BlockchainOrderFactory::TOKEN_SELL);
                    /** @var BlockchainContract $tokenBuy */
                    $tokenSell = end($tokensSell);

                    if(!($tokenSell instanceof RmrkContractStandard)){
                        $token = $tokenSell->getId();
                    }else{
                        $token = $this->blockchain->getMainCurrencyTicker();
                    }
                    $matchWith[$i]['token_sell'] = $token;

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
    private function filterKusamaOrders(BlockchainOrder $order): bool
    {
        return !is_null($order->getBuyDestination()) && $order->getBuyDestination()->getAddress() == $this->needleAddress;
    }





}