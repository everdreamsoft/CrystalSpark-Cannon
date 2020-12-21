<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

 class SubstrateBlockchain extends Blockchain
{
    protected $name = 'substrate';
    const NAME = 'substrate';
    protected $nameShort = 'substrate';
    private static $staticBlockchain;


    public function __construct()
    {
        $this->addressFactory = new SubstrateAddressFactory();
        $this->contractFactory = new SubstrateContractFactory();
        $this->eventFactory = new SubstrateEventFactory();
    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new SubstrateBlockchain();
        }

        return self::$staticBlockchain ;
    }
}
?>