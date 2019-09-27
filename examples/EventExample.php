<?php
namespace CsCannon ;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Counterparty\XcpEventFactory;
use CsCannon\Blockchains\Counterparty\XcpImporter;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload


$defaultXcpAddress = '186nXV8gY3LC1fjoTDGcieJqhk7ETgmPNM';

//$xcpEventImporter = new XcpImporter(SandraManager::getSandra());
//$xcpEventImporter->getEvents();
//die();

//$xcpContractFactory = new XcpContractFactory();
//$xcpContractFactory->populateLocal();
//print_r($xcpContractFactory->getDisplay('array'))
  //  die();


//$address = $defaultXcpAddress ;

$addressArray = null;

$allTransactionsArray = array();
$rawAddress = '';

$limit = 100;
$offset = 0;

if (isset($address)) {
    $addressArray = explode(',', $address);
}


if (is_array($addressArray)) {

    foreach ($addressArray as $key => $address) {

        $addressFactory = BlockchainRouting::getAddressFactory($address);
        $addressEntity = $addressFactory->get($address, true);

        $rawAddress = $address;


        if (is_null($addressEntity->subjectConcept)) continue; //the address doens't exist in the system


        $eventFactory = BlockchainRouting::getEventFactory($address);
        $eventFactory2 = BlockchainRouting::getEventFactory($address);
        $allTransactionsArray += getEventsSegment($eventFactory, $limit, $offset, $addressEntity, array(BlockchainEventFactory::EVENT_SOURCE_ADDRESS => $addressEntity));
        $allTransactionsArray += getEventsSegment($eventFactory2, $limit, $offset, $addressEntity, array(BlockchainEventFactory::EVENT_DESTINATION_VERB => $addressEntity));


    }


}
else {

    //all events requested


    //$blockchainEventFactory = new EthereumEventFactory();
    //$allTransactionsArray += $this->getEventsSegment($blockchainEventFactory, $limit, $offset, $address,$filter);

    $blockchainEventFactory = new XcpEventFactory();
    $allTransactionsArray += getEventsSegment($blockchainEventFactory, $limit, $offset, $address,$filter);

}

print_r($allTransactionsArray);


 function getEventsSegment($eventFactory, $limit, $offset, $addressEntity = null, $filters = array())
{


    //$addressFactory = BlockchainRouting::getAddressFactory($address);


    //$addressEntity = $addressFactory->get($address);
    $returnArray = array();


    $blockchainEnventFactory = $eventFactory;


    foreach ($filters as $key => $value) {

        $blockchainEnventFactory->setFilter($key, $value);
    }


    $blockchainEnventFactory->populateLocal($limit, $offset);


    $tokenData = array();

    foreach ($blockchainEnventFactory->entityArray as $eventEntity) {

        $contractAdress = null;

        /** @var BlockchainEvent $eventEntity */
        $source = $eventEntity->getSourceAddress();
        try {
            $contract = $eventEntity->getBlockchainContract();
            if ($contract instanceof BlockchainContract or $contract) {
                $contractAdress = $contract->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

                // removed this because if we load image for each token ist too heavy
                // $eventData['asset'] = $contract->resolveMetaData($eventEntity->getTokenId());
                $eventData['asset'] = $contract->resolveMetaData();
                $collection = $contract->getCollections();
            }


        } catch (\Exception $e) {
            /** @var BlockchainContract $contract */

            $contractAdress = 'null';


            //  continue ;
        }

        /** @var BlockchainContract $contract */

        // echo" $source \n ";


        $timestamp = $eventEntity->get('timestamp');
        $arrayKey = $timestamp . '.' . $eventEntity->get(Blockchain::$txidConceptName);

        $eventData['tokenId'] = $eventEntity->getTokenId();
        // $eventData['opensea'] =  $eventEntity->get('openSeaId');
        $eventData['source'] = $source;
        $eventData['destination'] = $eventEntity->getDestinationAddress();
        $eventData['quantity'] = $eventEntity->get('quantity');
        $eventData['timestamp'] = $eventEntity->get('timestamp');
        // $eventData['collectionImage'] = $contract->

        $eventData['contract'] = $contractAdress;


        $eventData['txHash'] = $eventEntity->get(Blockchain::$txidConceptName);
        $eventData['tokenData'] = $tokenData;

        //$joinedAssets = $contract->get

        $returnArray[$arrayKey] = $eventData;

        $contract = null;

    }

    //die();


    return $returnArray;


}
