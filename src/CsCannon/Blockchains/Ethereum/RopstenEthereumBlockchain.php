<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Blockchains\Blockchain;


class RopstenEthereumBlockchain extends EthereumBlockchain
{

   protected $name = 'ropsten_ethereum';
   const NAME = 'ropsten_ethereum';
    protected $nameShort = 'ropsten_eth';
    private static $staticBlockchain ;
    public static $network = array("mainet"=>array("explorerTx"=>'https://ropsten.etherscan.io/tx/'),

    );

    public function __construct()
    {


        $this->eventFactory = new RopstenEventFactory();
        parent::__construct();

    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new RopstenEthereumBlockchain();

        }

        return self::$staticBlockchain ;

    }










}