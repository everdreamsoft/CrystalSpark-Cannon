<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\RMRK\RmrkBlockchainOrderProcess;
use CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class WestendBlockchain extends SubstrateBlockchain
{
    protected $name = 'westend';
    const NAME = 'westend';
    protected $nameShort = 'ksm';
    private static $staticBlockchain;

    public function __construct()
    {

        $this->orderProcess = new RmrkBlockchainOrderProcess($this);
        $this->contractFactory = new RmrkContractFactory(WestendBlockchain::class);     //careful with this
        $this->eventFactory = new WestendEventFactory();
        $this->addressFactory = new WestendAddressFactory();

    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new WestendBlockchain();
        }

        return self::$staticBlockchain ;
    }
}
