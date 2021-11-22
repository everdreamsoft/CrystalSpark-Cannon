<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Blockchains\Blockchain;

class BinanceBlockchain extends Blockchain
{
    protected $name = 'binance';
    const NAME = 'binance';
    protected $nameShort = 'bsc';
    private static $staticBlockchain ;
    public static $network = array("mainet"=>array("explorerTx"=>'https://bscscan.com/tx/'));
    public  $mainSourceCurrencyTicker = 'BNB' ;

    public function __construct()
    {
        $this->addressFactory = new BinanceAddressFactory();
        $this->contractFactory = new BinanceContractFactory();
        $this->eventFactory = new BinanceEventFactory();
    }

    public static function getStatic()
    {
        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new BinanceBlockchain();
        }
        return self::$staticBlockchain ;
    }

}