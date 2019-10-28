<?php

namespace CsCannon;




use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class BlockchainRouting
{

    public static function getSupportedBlockchains(){

        $supported[] = new XcpBlockchain();
        $supported[] = new EthereumBlockchain();

        return $supported ;

    }

   public static function blockchainFromAddress($address){

        //as for today 0x means ethereum
       if(substr( $address, 0, 2 ) === "0x"){

           $blockchainList['eth'] = $address ;
           $blockchain = new EthereumBlockchain();


       }

       //as Force klaytn
       if(substr( $address, 0, 2 ) === "0x"){

           $blockchainList['klay'] = $address ;
           $blockchain = new KlaytnBlockchain();


       }

       else if (substr( $address, 0, 2 ) === "3P"){

           $blockchainList['waves'] = $address;
           //$blockchain = new Waves();

       }


   else{

           $blockchainList['xcp'] = $address;
           $blockchain = new XcpBlockchain();

       }

       return $blockchain ;


   }

    public static function getAddressFactory($deducable):BlockchainAddressFactory{


        $blockchain = self::blockchainFromAddress($deducable);


        return  $blockchain->getAddressFactory() ;


    }

    public static function getContractFactory($deducable){


        $blockchain = self::blockchainFromAddress($deducable);

        return  $blockchain->getContractFactory() ;


    }

    public static function getEventFactory($deducable):BlockchainEventFactory {


        $blockchain = self::blockchainFromAddress($deducable);

        return  $blockchain->getEventFactory() ;


    }


    public static function getDataPath($blockchain,$type){


       switch  ($blockchain) {

           case 'xcp' :
               $path['eventFile'] = 'btcBlockchainEventFile';
               $path['eventIs_a'] = 'btcBlockchainEvent';
               $path['addressFile'] = 'btcAddressFile';
               $path['addressIs_a'] = 'btcAddress';

               break ;



       }

        if (isset($path[$type]))
            return $path[$type] ;

       return null ;

    }

}
