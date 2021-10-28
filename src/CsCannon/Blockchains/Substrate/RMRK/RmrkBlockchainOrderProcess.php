<?php


namespace CsCannon\Blockchains\Substrate\RMRK;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
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



    public function makeMatchOneByOne($verbose = false): ?bool
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
                return false;
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
     * @param $blockchain
     * @return bool
     */
    public function listsCancellation($blockchain): bool
    {
        $orderFactory = new BlockchainOrderFactory($blockchain);
        $lists = $orderFactory->getLastListCancellation();

        print_r(count($lists).PHP_EOL);

        if(empty($lists)){
            return false;
        }

        $listToCancel = reset($lists);

        $contract = $listToCancel->getContractToSell();

        try{
            $token = $listToCancel->getTokenSell();
            $sn = $token->getDisplayStructure();
            $verb = BlockchainEventFactory::TOKEN_SELL;
        }catch (Exception $e){
            $token = $listToCancel->getTokenBuy();
            $sn = $token->getDisplayStructure();
            $verb = BlockchainEventFactory::TOKEN_BUY;
        }
        $source = $listToCancel->getSourceAddress();

        $factory = new BlockchainOrderFactory($blockchain);
        $factory->setFilter(BlockchainEventFactory::EVENT_SOURCE_ADDRESS, $source);
        $factory->setFilter($verb, $token);
        $factory->setFilter(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $contract);
        $factory->setFilter(BlockchainOrderFactory::STATUS, 0 , true);
        $factory->setFilter(BlockchainOrderFactory::BUY_DESTINATION, 0 , true);
        $factory->populateLocal();

        /** @var BlockchainOrder[] $entities */
        $entities = $factory->getEntities();
        usort($entities, [$this, "sortByTimestamp"]);
        print_r(count($entities));

        if(!empty($entities)){

            if(count($entities) > 1){
                $this->listCancellation($entities);
            }else{
                $listToCancel->setBrotherEntity(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CANCELLED, null, true, true);
            }
        }
        return true;
    }


    /**
     * @param $limit
     * @param $blockchain
     * @return bool
     */
    public function cancelLists($limit, $blockchain): bool
    {
        $orderFactory = new BlockchainOrderFactory($blockchain);
//        $orderFactory->setFilter(BlockchainOrderFactory::STATUS, 0 , true);
//        $orderFactory->setFilter(BlockchainOrderFactory::BUY_DESTINATION, 0 , true);
//        $orderFactory->populateLocal($limit, $offset);

//        $orders = $orderFactory->getEntities();
        $orders = $orderFactory->getLastListCancellation();


        // TODO filter before populate in populateFromSearchResult
        $orders = array_filter($orders, [$this, "filterIsList"]);
        $orders = array_filter($orders, [$this, "filterStatus"]);

        if(empty($orders)){
            return false;
        }

        $i = 0;
        foreach ($orders as $order){

            if($i >= $limit){
                return true;
            }

            $sourceAddress = $order->getJoinedEntities(BlockchainEventFactory::EVENT_SOURCE_ADDRESS)?? null;
            /** @var BlockchainContract[] $contractSell */
            $contractSell = $order->getJoinedEntities(BlockchainOrderFactory::ORDER_SELL_CONTRACT)?? null;

            // Verifiy is another list with same address and contract exists
            if(!is_null($sourceAddress) && !is_null($contractSell)){

                // get List data for search
                $contractSell = reset($contractSell);
                $sourceAddress = reset($sourceAddress);

                print_r($contractSell->getReference(BlockchainContractFactory::MAIN_IDENTIFIER)->refValue ?? null);
                print_r(PHP_EOL);

                try{
                    $token = $order->getTokenSell();
                    $sn = $token->getDisplayStructure();
                    $tokenVerb = BlockchainEventFactory::TOKEN_SELL;
                }catch (\Exception $e){
                    $token = $order->getTokenBuy();
                    $sn = $token->getDisplayStructure();
                    $tokenVerb = BlockchainEventFactory::TOKEN_BUY;
                }

                // Search all Lists with the same data (contract, source and token)
                $newOrderFact = new BlockchainOrderFactory($blockchain);
                $newOrderFact->setFilter(BlockchainOrderFactory::ON_BLOCKCHAIN, $blockchain::NAME);
                $newOrderFact->setFilter(BlockchainEventFactory::EVENT_SOURCE_ADDRESS, $sourceAddress);
                $newOrderFact->setFilter(BlockchainOrderFactory::STATUS, 0 , true);
                $newOrderFact->setFilter(BlockchainOrderFactory::BUY_DESTINATION, 0 , true);
                $newOrderFact->setFilter(BlockchainOrderFactory::ORDER_SELL_CONTRACT, $contractSell);
                $newOrderFact->setFilter($tokenVerb, $token);
                $newOrderFact->populateLocal();

                /** @var BlockchainOrder[] $allLists */
                $allLists = $newOrderFact->getEntities();

                print_r(count($allLists).PHP_EOL);

                // verify if at least one list is a cancellation
                $cancellationInArray = array_filter($allLists, [$this, "findListCancellation"]);

                // If there is a cancellation and if there is at least 2 Lists
                if(!empty($cancellationInArray) && count($allLists) > 1){
                    $this->listCancellation(array_reverse($allLists));
                }else{
                    $order->setBrotherEntity(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CANCELLED, null, true, true);
                }
            }
            $i++;
        }
        return true;
    }


    /**
     * @param array $lists
     * @return array
     */
    private function listCancellation(array $lists)
    {
        $listsToCancel = [];
        $cancelListPushed = false;

        foreach ($lists as $list){

            if(!$cancelListPushed){
                if($list->getContractToBuyQuantity() == '0' || $list->getContractToSellQuantity() == '0'){
                    $listsToCancel[] = $list;
                    $cancelListPushed = true;
                }
            }else if($list->getContractToBuyQuantity() != "0" || $list->getContractToSellQuantity() != '0'){
                $listsToCancel[] = $list;
            }

            if(count($listsToCancel) == 2){
                print_r("annulation");
                foreach ($listsToCancel as $listToCancel){
                    $listToCancel->setBrotherEntity(BlockchainOrderFactory::STATUS, BlockchainOrderFactory::CANCELLED, null, true, true);
                }
                $listsToCancel = [];
            }
        }
        return $lists;
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


    /**
     * @param BlockchainOrder $list
     * @return bool
     */
    private function findListCancellation(BlockchainOrder $list)
    {
        return $list->getContractToBuyQuantity() == '0';
    }


    private function sortByTimestamp(BlockchainOrder $list, BlockchainOrder $secondList)
    {
        $timestamp = $list->getReference(BlockchainOrderFactory::EVENT_BLOCK_TIME)->refValue ?? null;
        $secondTimestamp = $secondList->getReference(BlockchainOrderFactory::EVENT_BLOCK_TIME)->refValue ?? null;

        if($timestamp === $secondTimestamp){
            return 0;
        }
        return ($timestamp > $secondTimestamp) ? -1 : 1;
    }

    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    public function filterStatus(BlockchainOrder $order): bool
    {
        return is_null($order->getBrotherEntity(BlockchainOrderFactory::STATUS));
    }

    /**
     * @param BlockchainOrder $order
     * @return bool
     */
    public function filterIsList(BlockchainOrder $order): bool
    {
        return is_null($order->getBuyDestination());
    }


}
