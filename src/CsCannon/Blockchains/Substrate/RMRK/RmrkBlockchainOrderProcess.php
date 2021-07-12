<?php


namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
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
     * @return array
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
            if(is_null($isClose) || $isClose->refValue != BlockchainOrderFactory::CLOSE){

                $needleContractToBuyId = $needleMatch->getContractToBuy()->getReference('id')->refValue;
                $matchContractToBuyId = $matchOrder->getContractToBuy()->getReference('id')->refValue;

                $needleContractToSellId = $needleMatch->getReference('id')->refValue;
                $matchContractToSellId = $matchOrder->getReference('id')->refValue;

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

            }
        }

        return $matches;
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