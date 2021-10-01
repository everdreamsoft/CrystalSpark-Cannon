<?php


namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainOrder;
use CsCannon\Blockchains\BlockchainOrderFactory;
use CsCannon\Blockchains\DataSource\DatagraphSource;
use CsCannon\Tools\BlockchainOrderProcess;
use Exception;
use SebastianBergmann\CodeCoverage\Report\PHP;


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
    public function getAllMatches()
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



    public function makeMatchOneByOne($verbose = false)
    {

        $verbose ? print_r( "starting match order" ) : false ;

        $orderFactory = new BlockchainOrderFactory($this->blockchain);
        /** @var BlockchainOrder $lastBuy */
        $lastBuy = $orderFactory->getLastBuy();



        if($lastBuy){

            $verbose ? print_r( date(DATE_RFC2822,$lastBuy->getBlockTimestamp()).PHP_EOL) : false ;
            $verbose ? print_r( "order buy TX : ".$lastBuy->getTxId().PHP_EOL) : false ;

            $contractToBuy = $lastBuy->getContractToBuy();

            $sellerAddress = $lastBuy->getBuyDestination();


            $factory = new BlockchainOrderFactory($this->blockchain);
            $factory->setFilter(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $contractToBuy);
            $factory->setFilter(BlockchainOrderFactory::STATUS, 0, true);
            $factory->setFilter(BlockchainOrderFactory::EVENT_SOURCE_ADDRESS, $sellerAddress);
            $factory->populateLocal(1);

            /** @var BlockchainOrder[] $sellOrders */
            $sellOrders = $factory->getEntities();

            if(count($sellOrders) < 1){
                $verbose ? print_r("no sell order".PHP_EOL) : false;
                $lastBuy->closeOrder();
                return null;
            }

            $sellOrder = end($sellOrders);

            $verbose ? print_r( "order sell match candidate TX : ".$sellOrder->getTxId().PHP_EOL) : false ;
            $verbose ? print_r( date(DATE_RFC2822,$lastBuy->getBlockTimestamp()).PHP_EOL) : false ;

            try{
                $matchMaking = $this->makeOneKusamaMatch($lastBuy, $sellOrder,$verbose);
            }catch(Exception $e){
                return false;
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
     * @param bool $verbose
     * @return bool
     * @throws Exception
     */
    public function makeOneKusamaMatch(BlockchainOrder $buyOrder, BlockchainOrder $sellOrder, $verbose=false): bool
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
                    $verbose ? print_r( "We are matching the order".PHP_EOL) : false ;

                    $this->sendMatchAndUpdate($sellOrder, $buyOrder);

                    $matched = true;
                    $verbose ? print_r( "Sucessfuly matched ".PHP_EOL) : false ;

                }catch(Exception $e){
                    throw $e;
                }
            }
        }


        $verbose ? print_r("closing buy and sell order") : false ;
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