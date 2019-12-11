<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:55
 */

namespace CsCannon\Blockchains;


use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Balance;
use SandraCore\ForeignEntityAdapter;

 abstract class BlockchainDataSource
{


    protected static $localCollections = null ;


     public static function initWithCollection(AssetCollectionFactory $localCollections = null)
     {

         self::$localCollections = $localCollections ;
         return self::class ;

     }

     public static function getAddressString($address)
     {

         if ($address instanceof BlockchainAddress) return $address->getAddress();
         if (is_string($address)) return $address ;
         return null ;

     }

     public static function getLocalCollection(AssetCollectionFactory $localCollections = null)
     {

         self::$localCollections = $localCollections ;
         return self::class ;

     }

     public static function init()
     {



     }


     public static abstract function getEvents($contract=null,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter ;




    public static abstract function getBalance(BlockchainAddress $address,$limit,$offset):Balance ;

}