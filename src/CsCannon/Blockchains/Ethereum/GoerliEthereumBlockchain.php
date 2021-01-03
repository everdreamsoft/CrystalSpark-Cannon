<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Blockchains\Blockchain;


class GoerliEthereumBlockchain extends EthereumBlockchain
{

   protected $name = 'goerli';
   const NAME = 'goerli';
    protected $nameShort = 'goerli';
    private static $staticBlockchain ;
    public static $network = array("mainet"=>array("explorerTx"=>'https://ropsten.etherscan.io/tx/'),

    );

    public function __construct()
    {


        $this->eventFactory = new EthereumEventFactory();
        parent::__construct();

    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new GoerliEthereumBlockchain();

        }

        return self::$staticBlockchain ;

    }










}