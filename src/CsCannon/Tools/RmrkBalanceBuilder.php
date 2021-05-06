<?php


namespace CsCannon\Tools;


use CsCannon\AssetCollection;
use CsCannon\Blockchains\BlockchainEvent;

class RmrkBalanceBuilder extends BalanceBuilder
{


    protected  static function hasSendError(BlockchainEvent $event):?string{

       $error = parent::hasSendError($event);

        $contract = $event->getBlockchainContract();
        $collections = $contract->getCollections();
        //RMRK should have one collection for contract
        $firstCollection = end($collections);

        if ($firstCollection instanceof AssetCollection){

          $maxSupply =  $firstCollection->get('maxSupply');
        }


        return $error ;


    }


}