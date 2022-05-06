<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Blockchains\Blockchain;


class EthereumBlockchain extends Blockchain
{

   protected $name = 'ethereum';
   const NAME = 'ethereum';
    protected $nameShort = 'eth';
    private static $staticBlockchain ;
    public static $network = array("mainet"=>array("explorerTx"=>'https://etherscan.io/tx/'),

    );

    public static $blockchainConceptName = 'ethereum';

    public  $mainSourceCurrencyTicker = 'ETH' ;

    public function __construct()
    {

        $this->addressFactory = new EthereumAddressFactory();
        $this->contractFactory = new EthereumContractFactory();
        $this->eventFactory = new EthereumEventFactory();

    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new EthereumBlockchain();

        }

        return self::$staticBlockchain ;

    }










}
