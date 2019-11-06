<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Klaytn;



use CsCannon\Blockchains\Blockchain;


class KlaytnBlockchain extends Blockchain
{

   protected $name = 'klaytn';
   const NAME = 'klaytn';
    protected $nameShort = 'klay';
    private static $staticBlockchain ;
    public static $network = array("cypress"=>array("explorerTx"=>'https://scope.klaytn.com/tx/'),
    "baobab"=>array("explorerTx"=>'https://baobab.scope.klaytn.com/tx/')
    );
    public function __construct()
    {

        $this->addressFactory = new KlaytnAddressFactory();
        $this->contractFactory = new KlaytnContractFactory();
        $this->eventFactory = new KlaytnEventFactory();

    }


    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new KlaytnBlockchain();

        }

        return self::$staticBlockchain ;

    }










}