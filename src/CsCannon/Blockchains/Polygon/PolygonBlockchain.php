<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */

namespace CsCannon\Blockchains\Polygon;

use CsCannon\Blockchains\Blockchain;

class PolygonBlockchain extends Blockchain
{

    protected $name = 'polygon';
    const NAME = 'polygon';
    protected $nameShort = 'polygon';

    private static $staticBlockchain;

    public static $network = array(
        "mainnet" => array("explorerTx" => 'https://polygonscan.com/tx/'),
        "mumbai" => array("explorerTx" => 'https://mumbai.polygonscan.com/tx/'),
    );

    public $mainSourceCurrencyTicker = 'MATIC';

    public function __construct()
    {
        $this->addressFactory = new PolygonAddressFactory();
        $this->contractFactory = new PolygonContractFactory();
        $this->eventFactory = new PolygonEventFactory();
    }

    public static function getStatic()
    {
        if (is_null(self::$staticBlockchain)) {
            self::$staticBlockchain = new PolygonBlockchain();
        }
        return self::$staticBlockchain;
    }

}
