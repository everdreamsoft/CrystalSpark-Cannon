<?php


namespace CsCannon\Tools;


use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory;
use SandraCore\DatabaseAdapter;
use SandraCore\EntityFactory;

class BalanceBuilder
{

    const PROCESS_STATUS_VERB = 'processedStatus';
    const PROCESS_STATUS_VALID = 'valid';
    const PROCESS_STATUS_PENDING = 'pending';
    const PROCESS_STATUS_INVALID = 'invalid';
    const PROCESS_STATUS_REVERTED = 'reverted';
    const PROCESSOR_ERROR = 'error';

    const PROCESSOR_CONCEPT = 'processor';

    const PROCESSOR_NAME = 'CSCannonBalanceBuilder';
    const PROCESSOR_VERSION = '0.1';

    private static $bufferBalance = [];

    public static function buildBalance(BlockchainEventFactory $eventFactory,$verbose = false)
    {

        $maxProcess = 10000 ;
        self::$bufferBalance = [];

        $eventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING);
        $eventFactory->populateLocal($maxProcess, 0, 'ASC',BlockchainEventFactory::EVENT_BLOCK_TIME,true);
        $count = 0 ;
        $somethingToCommit = false ;

        foreach ($eventFactory->getEntities() as $event) {
            /** @var BlockchainEvent $event */
            if ($event->getDestinationAddress()) {

                $error = static::hasSendError($event);
                $verbose ? print_r( date(DATE_RFC2822,$event->getBlockTimestamp())) : false ;
                $verbose ? print_r($event->get(Blockchain::$txidConceptName). " ".$event->get(BlockchainEventFactory::EVENT_BLOCK_TIME)) : false ;
                //print_r($event->get(Blockchain::$txidConceptName). " ".$event->get(BlockchainEventFactory::EVENT_BLOCK_TIME)  .PHP_EOL);
                // echo"looping once ".$count.PHP_EOL;
                $count++;

                if (!$error){
                    $contract = $event->getBlockchainContract();
                    $quantity = $event->getQuantity();
                    $token = $event->getSpecifier();
                    $newBalance = self::getAddressBalance($event->getDestinationAddress());
                    $newBalance->addContractToken($event->getBlockchainContract(),$token,$quantity);

                    $somethingToCommit = true ;

                    $oldBalance = self::getAddressBalance($event->getSourceAddress());
                    $oldBalancePreviousQuantity = $oldBalance->getQuantityForContractToken($contract,$token);
                    $oldBalance->addContractToken($contract,$token,$oldBalancePreviousQuantity-$quantity);
                    // $oldBalance->saveToDatagraph();

                    $event->createOrUpdateRef(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID,false);
                    $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],false,true);
                    $somethingToCommit = true ;
                    $verbose ? print_r(" valid".PHP_EOL) : false ;

                }
                else{
                    $event->createOrUpdateRef(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID,false);
                    $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME,
                        self::PROCESSOR_ERROR=>$error
                    ],false,true);

                    $verbose ? print_r(" Invalid".PHP_EOL) : false ;
                }
            }
        }
        if ($somethingToCommit) {
            try {
                $verbose ? print_r(" DB commit".PHP_EOL) : false ;
                DatabaseAdapter::commit();
            } catch (\Exception $e) {
                //nothing to commit
            }
        }
        $verbose ? print_r(" Save buffer".PHP_EOL) : false ;
        self::saveBalanceBuffer();
        $verbose ? print_r(" Buffer saved".PHP_EOL) : false ;
    }

    private static function getAddressBalance(BlockchainAddress $address){

        if (!isset(self::$bufferBalance[$address->getAddress()])){

            self::$bufferBalance[$address->getAddress()] = $address->getBalance(1000000);
        }

        return  self::$bufferBalance[$address->getAddress()];

    }

    private static function saveBalanceBuffer(){

        foreach (self::$bufferBalance as $balance){

            $balance->saveToDatagraph();

        }

        self::$bufferBalance = [];

    }

    protected static function hasSendError(BlockchainEvent $event):?string{

        $nullAddress = BlockchainAddressFactory::NULL_ADDRESS ;
        $contract = $event->getBlockchainContract();
        $quantity = $event->getQuantity();
        $token = $event->getSpecifier();
        $actualBalance = self::getAddressBalance($event->getSourceAddress());
        $sourceAddressBalance = self::getAddressBalance($event->getSourceAddress());

        $quantityOwned = $sourceAddressBalance->getQuantityForContractToken($contract, $token);

        if ($nullAddress != $event->getSourceAddress()->getAddress()){

        }

        //exeption if it's a mint
        if ($quantityOwned < $quantity && $nullAddress != $event->getSourceAddress()->getAddress()) {
            return 'not enough quantity for sender' ;
        }

        return null ;

    }

    public static function resetBalanceBuilder(BlockchainEventFactory $eventFactory,$verbose = false){

        $maxProcess = 10000 ;


        $factory = new EntityFactory('balanceItem','balanceFile',$eventFactory->system);
        $factory->populateLocal(100000,null,null,'quantity',true);

        $somethingToCommit = false ;

        foreach ($factory->getEntities() ?? array() as $balanceItem){

            // print_r($balanceItem->dumpMeta());
            if ($balanceItem->get('quantity') != 0) {
                $balanceItem->createOrUpdateRef('quantity', 0, false);
                $somethingToCommit = true ;
            }
        }
        $verbose ? print_r( "commit balance".PHP_EOL) : false ;
        $somethingToCommit ? DatabaseAdapter::commit() : false ;
        $verbose ? print_r( "finished balance commit".PHP_EOL) : false ;

        //return ;




        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID);
        $events = static::getEventsFromFactory($copiedEventFactory);

        $verbose ? print_r( "we revert valid transactions") : false ;
        $somethingToCommit = 0 ;
        //we revert balance of all valid trandactions
        while ($events = static::getEventsFromFactory($copiedEventFactory)) {
            $verbose ? print_r( "looping on events count :".count($events).PHP_EOL) : false ;
            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

                $verbose ? print_r( "event :".$event->get(BlockchainEventFactory::EVENT_BLOCK_TIME) .PHP_EOL) : false ;
                $verbose ? print_r( " event :".$event->subjectConcept->idConcept) : false ;


                //$token = $event->getSpecifier();
                //$newBalance = $event->getDestinationAddress()->getBalance();
                //$newBalance->addContractToken($event->getBlockchainContract(), $token, 0);
                //$newBalance->saveToDatagraph();

                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],false,true);
                $somethingToCommit ++ ;

            }

            $factoryClass = get_class($eventFactory);
            $copiedEventFactory = new  $factoryClass ;
            $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID);
            $events = static::getEventsFromFactory($copiedEventFactory);


        }

        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID);
        $events = static::getEventsFromFactory($copiedEventFactory);

        $verbose ? print_r( "we revert invalid transactions").PHP_EOL : false ;
        //we revert balance invalid trandactions
        while ($events) {
            $verbose ? print_r( "looping on events count :".count($events).PHP_EOL) : false ;
            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],false,true);
                $somethingToCommit ++ ;

            }
            $factoryClass = get_class($eventFactory);
            $copiedEventFactory = new  $factoryClass ;
            $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID);
            $events = static::getEventsFromFactory($copiedEventFactory);
        }

        echo "to commit" . $somethingToCommit . PHP_EOL ;
        $somethingToCommit ? DatabaseAdapter::commit() : false ;


        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        self::flagAllForValidation($copiedEventFactory,$verbose);

    }

    public static function flagAllForValidation (BlockchainEventFactory $eventFactory,$verbose = false){

        $maxProcess = 1000 ;

        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,0,true);
        $events = static::getEventsFromFactory($copiedEventFactory);

        $verbose ? print_r( "flag events for validation :".count($events).PHP_EOL) : false ;
        while ($events){

            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

                $verbose ? print_r( "event :".$event->get(BlockchainEventFactory::EVENT_BLOCK_TIME) .PHP_EOL) : false ;
                $verbose ? print_r( " event :".$event->subjectConcept->idConcept) : false ;

                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],
                    true,true);

            }



            $factoryClass = get_class($eventFactory);
            $copiedEventFactory = new  $factoryClass ;
            $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,0,true);
            $events = static::getEventsFromFactory($copiedEventFactory);
        }


    }

    private static function getEventsFromFactory(BlockchainEventFactory $factory){

        $maxProcess = 10000 ;

        $factory->populateLocal($maxProcess, 0, 'ASC',BlockchainEventFactory::EVENT_BLOCK_TIME,true);

        return $factory->getEntities();

    }



}

