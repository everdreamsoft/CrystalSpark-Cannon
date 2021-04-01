<?php


namespace CsCannon\Tools;


use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainEvent;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Substrate\Kusama\KusamaEventFactory;
use SandraCore\DatabaseAdapter;

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

    public static function buildBalance(BlockchainEventFactory $eventFactory)
    {

        $maxProcess = 10000 ;
        self::$bufferBalance = [];

        $eventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING);
        $eventFactory->populateLocal($maxProcess, 0, 'ASC');
        $count = 0 ;

        foreach ($eventFactory->getEntities() as $event) {
            /** @var BlockchainEvent $event */
            if ($event->getDestinationAddress()) {

            $error = static::hasSendError($event);
            //echo"looping once ".$count.PHP_EOL;
            $count++;

            if (!$error){
                $contract = $event->getBlockchainContract();
                $quantity = $event->getQuantity();
                $token = $event->getSpecifier();
                $newBalance = self::getAddressBalance($event->getDestinationAddress());
                $newBalance->addContractToken($event->getBlockchainContract(),$token,$quantity);
                //$newBalance->saveToDatagraph();


                $oldBalance = self::getAddressBalance($event->getSourceAddress());
                $oldBalancePreviousQuantity = $oldBalance->getQuantityForContractToken($contract,$token);
                $oldBalance->addContractToken($contract,$token,$oldBalancePreviousQuantity-$quantity);
               // $oldBalance->saveToDatagraph();

                $event->createOrUpdateRef(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID,false);
                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],false,true);

            }
            else{
                $event->createOrUpdateRef(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID,false);
                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME,
                    self::PROCESSOR_ERROR=>$error
                    ],false,true);
            }




            }
        }
        DatabaseAdapter::commit();
        self::saveBalanceBuffer();
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

    private static function hasSendError(BlockchainEvent $event):?string{

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

    public static function resetBalanceBuilder(BlockchainEventFactory $eventFactory){

        $maxProcess = 10000 ;

        $eventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID);
        $eventFactory->populateLocal($maxProcess, 0, 'ASC');

        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_VALID);
        $events = static::getEventsFromFactory($copiedEventFactory);


        //we revert balance of all valid trandactions
        while ($events = static::getEventsFromFactory($copiedEventFactory)) {
            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

                $token = $event->getSpecifier();
                $newBalance = $event->getDestinationAddress()->getBalance();
                $newBalance->addContractToken($event->getBlockchainContract(), $token, 0);
                $newBalance->saveToDatagraph();

                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],true,true);

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

        //we revert balance invalid trandactions
        while ($events) {
            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

                $event->setBrotherEntity(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_PENDING,[self::PROCESSOR_CONCEPT=>static::PROCESSOR_NAME],true,true);

            }
            $factoryClass = get_class($eventFactory);
            $copiedEventFactory = new  $factoryClass ;
            $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,static::PROCESS_STATUS_INVALID);
            $events = static::getEventsFromFactory($copiedEventFactory);
        }

    }

    public static function flagAllForValidation (BlockchainEventFactory $eventFactory){

        $maxProcess = 1000 ;

        $factoryClass = get_class($eventFactory);
        $copiedEventFactory = new  $factoryClass ;
        $copiedEventFactory->setFilter(static::PROCESS_STATUS_VERB,0,true);
        $events = static::getEventsFromFactory($copiedEventFactory);

        while ($events){
            foreach ($events as $event) {
                /** @var BlockchainEvent $event */

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

        $factory->populateLocal($maxProcess, 0, 'ASC');

        return $factory->getEntities();

    }



}

