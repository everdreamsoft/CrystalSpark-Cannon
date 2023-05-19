<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;

use CsCannon\Blockchains\Blockchain;

class SepoliaEthereumBlockchain extends EthereumBlockchain
{
    protected $name = 'Sepolia';
    const NAME = 'Sepolia';
    protected $nameShort = 'Sepolia';
    private static $staticBlockchain;
    public static $network = array(
        "mainet" => array("explorerTx" => 'https://sepolia.etherscan.io/tx/'),
    );

    public function __construct()
    {
        $this->eventFactory = new EthereumEventFactory();
        parent::__construct();
    }

    public static function getStatic()
    {
        if (is_null(self::$staticBlockchain)) {
            self::$staticBlockchain = new SepoliaEthereumBlockchain();
        }
        return self::$staticBlockchain;
    }
}
