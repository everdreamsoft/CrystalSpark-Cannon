<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;



use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumEventFactory;


class MaticBlockchain extends EthereumBlockchain
{

   protected $name = 'matic';
   const NAME = 'matic';
    protected $nameShort = 'mat';
    private static $staticBlockchain ;

    public function __construct()
    {


        $this->addressFactory = new MaticAddressFactory();
        $this->contractFactory = new MaticContractFactory();
        $this->eventFactory = new MaticEventFactory();



    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new MaticBlockchain();

        }

        return self::$staticBlockchain ;

    }










}