<?php

namespace CsCannon;




use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Counterparty\XcpBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;

use CsCannon\Blockchains\FirstOasis\FirstOasisBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class BlockchainRouting
{

    /**
     * get supported blockchains by the framework
     * @return Blockchain[]
     */
    public static function getSupportedBlockchains(){

        $supported[] = new XcpBlockchain();
        $supported[] = new EthereumBlockchain();
        //$supported[] = new MaticBlockchain();
        $supported[] = new KlaytnBlockchain();
        $supported[] = new FirstOasisBlockchain();

        return $supported ;

    }

    public static function getBlockchainFromName($name):?Blockchain{

        $supported = self::getSupportedBlockchains();

        foreach ($supported as $blockchain){

            if ($name == $blockchain::NAME) return $blockchain ;

        }
        return null ;

    }

    /**
     * Deduct potential blockchains from an address
     *
     * @param $address
     * @return Blockchain[]
     */
    public static function getBlockchainsFromAddress($address){

        //as for today 0x means all ethereum type format
        if(substr( $address, 0, 2 ) === "0x"){

            $blockchainList['eth'] = $address ;
            $blockchains[] = new EthereumBlockchain();
            $blockchains[] = new KlaytnBlockchain();
            $blockchains[] = new \CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain();


        }

        else{

            $blockchainList['xcp'] = $address;
            $blockchains[] = new XcpBlockchain();

        }

        return $blockchains ;


    }

    /**
     * Get Address factories from an array of blockchains
     *
     * @param Blockchain[] $blockchainArray
     * @return BlockchainAddressFactory[]
     */
    public static function getAddressFactoriesFromBlockchains(array $blockchainArray){

        $addressFactories = array();

        foreach ($blockchainArray as $blockchain){

            $addressFactories[$blockchain::NAME] = $blockchain->getAddressFactory();
        }

        return $addressFactories ;

    }

    /**
     * Deduct potential blockchains from an address and return factories
     *
     * @param  $address
     * @return BlockchainAddressFactory[]
     */
    public static function getAddressFactoriesFromAddress($address){

        $blockchains = self::getBlockchainsFromAddress($address);
        $addressFactories = self::getAddressFactoriesFromBlockchains($blockchains);

        return $addressFactories ;

    }


   public static function blockchainFromAddress($address){

       //as Force klaytn
       if(substr( $address, 0, 2 ) === "0x"){

           $blockchainList['klay'] = $address ;
           $blockchain = new KlaytnBlockchain();


       }

        //as for today 0x means ethereum
       if(substr( $address, 0, 2 ) === "0x"){

           $blockchainList['eth'] = $address ;
           $blockchain = new EthereumBlockchain();


       }



       else if (substr( $address, 0, 2 ) === "3P"){

           $blockchainList['waves'] = $address;
           //$blockchain = new Waves();

       }

      else if(substr( $address, 0, 3 ) === "@f:"){

           $blockchainList['fo'] = $address ;
           $blockchain = new FirstOasisBlockchain();


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

    public static function getAddressFactories($address):BlockchainAddressFactory{


        $blockchain = self::blockchainFromAddress($address);


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
