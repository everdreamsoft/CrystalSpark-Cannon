<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\Blockchains\Blockchain;


class MaticBlockchain extends EthereumBlockchain
{

   protected $name = 'matic';
   const NAME = 'matic';
    protected $nameShort = 'mat';
    private static $staticBlockchain ;

    public function __construct()
    {



    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new MaticBlockchain();

        }

        return self::$staticBlockchain ;

    }










}