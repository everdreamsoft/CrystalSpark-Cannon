<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\Bitcoin\BtcBlockchain;


class OrdinalBlockchain extends BtcBlockchain
{
    const NAME = 'ordinal';

    protected $name = 'ordinal';
    protected $nameShort = 'ord';

    private static $staticBlockchain;

    public static $network = array("mainet" => array("explorerTx" => 'https://ordinals.com/tx/'));
    public $mainSourceCurrencyTicker = 'ORD';

    public function __construct()
    {
        $this->addressFactory = new OrdinalAddressFactory();
        $this->contractFactory = new OrdinalContractFactory();
        $this->eventFactory = new OrdinalEventFactory();
    }

    public static function getStatic()
    {
        if (is_null(self::$staticBlockchain)) {
            self::$staticBlockchain = new OrdinalBlockchain();
        }
        return self::$staticBlockchain;
    }

}
