<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;



use CsCannon\Blockchains\Bitcoin\BtcBlockchain;


 class XcpBlockchain extends BtcBlockchain
{

     protected $name = 'counterparty';
   const NAME = 'counterparty';
     protected $nameShort = 'xcp';
   private static $staticBlockchain ;

     public  $mainSourceCurrencyTicker = 'btc' ;

     public static $network = array("mainet"=>array("explorerTx"=>'https://xchain.io/tx/'),
         "testnet"=>array("explorerTx"=>'https://testnet.xchain.io/tx/')
     );



   public function __construct()
   {

       $this->addressFactory = new XcpAddressFactory();
       $this->eventFactory = new XcpEventFactory();
       $this->contractFactory = new XcpContractFactory();

   }

     public static function getStatic()
     {

       if (is_null(self::$staticBlockchain)){
           self::$staticBlockchain = new XcpBlockchain();

       }
       return self::$staticBlockchain ;

     }

 }
