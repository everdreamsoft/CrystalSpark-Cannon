<?php


namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Tools\BlockchainOrderProcess;
use Exception;


class RmrkBlockchainOrderProcess extends BlockchainOrderProcess
{

    public $needleAddress;

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct($blockchain);
    }


    /**
     * @return BlockchainOrder[]
     */
    public function getAllMatches(): array
    {
        $factory = new BlockchainOrderFactory($this->blockchain);
        $orders = $factory->getAllEntitiesOnChain();

        $matches = [];

        foreach ($orders as $orderOnChain){
            $match = $this->findKusamaMatchesOrders($orders, $orderOnChain);
            if(!empty($match)) $matches[] = $match;
        }

        return $matches;
    }


    /**
     * @return bool|null
     */
    public function makeMatchOneByOne(): ?bool
    {
        $orderFactory = new BlockchainOrderFactory($this->blockchain);
        /** @var BlockchainOrder $lastBuy */
        $lastBuy = $orderFactory->getLastBuy();

        if($lastBuy){

            $contractToBuy = $lastBuy->getContractToBuy();

            $factory = new BlockchainOrderFactory($this->blockchain);
            $factory->setFilter(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $contractToBuy);
            $factory->setFilter(BlockchainOrderFactory::STATUS, 0, true);
            $factory->populateLocal();

            /** @var BlockchainOrder[] $sellOrders */
            $sellOrders = $factory->getEntities();

            if(count($sellOrders) < 1){
                return null;
            }

            $sellOrder = end($sellOrders);

            try{
                $matchMaking = $this->makeOneKusamaMatch($lastBuy, $sellOrder);
            }catch(Exception $e){
                return null;
            }

            return $matchMaking;
        }
        return null;
    }



    /**
     * @param BlockchainOrder[] $orders
     * @param BlockchainOrder $needleMatch
     * @return array
     */
    public function findKusamaMatchesOrders(array $orders, BlockchainOrder $needleMatch): array
    {
        $kusamaMatches = [];

        $this->needleAddress = $needleMatch->getSource()->getAddress();
        $matchOrders = array_filter($orders, [$this, 'filterKusamaOrders']);

        if(!empty($matchOrders)){
            try{
                $kusamaMatches = $this->getKusamaMatches($matchOrders, $needleMatch);
            }catch(Exception $e){
                return [];
            }
        }

        return $kusamaMatches;
    }


    /**
     * @param BlockchainOrder $buyOrder
     * @param BlockchainOrder $sellOrder
     * @return bool
     * @throws Exception
     */
    public function makeOneKusamaMatch(BlockchainOrder $buyOrder, BlockchainOrder $sellOrder): bool
    {
        $buyContractToBuyId = $buyOrder->getContractToBuy()->getReference('id')->refValue;
        $sellContractToBuyId = $sellOrder->getContractToBuy()->getReference("id")->refValue;

        $buyContractToSellId = $buyOrder->getContractToSell()->getReference('id')->refValue;
        $sellContractToSellId = $sellOrder->getContractToSell()->getReference('id')->refValue;

        $matched = false;

        // One more checkout for contract's IDs
        if($buyContractToBuyId === $sellContractToSellId && $buyContractToSellId === $sellContractToBuyId){
            // Check quantities
            if($sellOrder->getContractToSellQuantity() >= $buyOrder->getContractToBuyQuantity() && $buyOrder->getContractToSellQuantity() >= $sellOrder->getContractToBuyQuantity()){

                try{
                    $this->sendMatchAndUpdate($sellOrder, $buyOrder);
                    $matched = true;
                }catch(Exception $e){
                    throw $e;
                }
            }
        }

        $sellOrder->closeOrder();
        $buyOrder->closeOrder();

        return $matched;
    }


    /**
     * @param BlockchainOrder[] $orders
     * @param BlockchainOrder $needleMatch
     * @return array
     * @throws Exception
     */
    public function getKusamaMatches(array $orders, BlockchainOrder $needleMatch): array
    {
        $matches = [];

        foreach ($orders as $matchOrder){

            $isClose = $matchOrder->getReference(BlockchainOrderFactory::STATUS);

            // check if orders don't have 'close' status
            if(is_null($isClose)){

                $needleContractToBuyId = $needleMatch->getContractToBuy()->getReference('id')->refValue;
                $matchContractToBuyId = $matchOrder->getContractToBuy()->getReference('id')->refValue;

                $needleContractToSellId = $needleMatch->getContractToSell()->getReference('id')->refValue;
                $matchContractToSellId = $matchOrder->getContractToSell()->getReference('id')->refValue;


                if($this->checkKusamaBalance($matchOrder)){
                    if($needleContractToBuyId === $matchContractToSellId && $needleContractToSellId === $matchContractToBuyId){
                        // check Contract ID

                        if($needleMatch->getContractToSellQuantity() >= $matchOrder->getContractToBuyQuantity() && $matchOrder->getContractToSellQuantity() >= $needleMatch->getContractToBuyQuantity()){
                            // Check quantities

                            try{
                                $this->sendMatchAndUpdate($matchOrder, $needleMatch);
                            }catch(Exception $e){
                                throw $e;
                            }

                            $matches[] = $matchOrder;
                        }
                    }
                }else{
                    $matchOrder->createOrUpdateRef(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::NOBALANCE);
                }

            }
        }

        return $matches;
    }


    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    public function checkKusamaBalance(BlockchainOrder $order): bool
    {
        $sourceAddress = $order->getSourceAddress();
        $addressBalance = DatagraphSource::getBalance($sourceAddress, null, null);

        $contractToSell = $order->getContractToSell() ?? null;
        $contractToSellId = $contractToSell->getReference("id");
        $ticker = $order->getBlockchain()->getMainCurrencyTicker();

        // if contractToSell is not Ksm, return balance != 0
        if(!is_null($contractToSellId) && $contractToSellId->refValue != $ticker){
            $tokenBalance = $addressBalance->getQuantityForContractToken($contractToSell, $order->getTokenSell());
            return $tokenBalance != 0;
        }

        return true;
    }



    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    public function filterKusamaOrders(BlockchainOrder $order): bool
    {
        return !is_null($order->getBuyDestination()) && $order->getBuyDestination()->getAddress() == $this->needleAddress;
    }





}