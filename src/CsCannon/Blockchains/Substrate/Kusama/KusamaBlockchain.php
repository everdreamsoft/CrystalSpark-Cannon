<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class KusamaBlockchain extends SubstrateBlockchain
{
    protected $name = 'kusamaBlockchain';
    const NAME = 'kusamaBlockchain';
    protected $nameShort = 'kusama';
    private static $staticBlockchain;

    public function __construct()
    {

        //carful with this
        $this->contractFactory = new RmrkContractFactory();
        $this->eventFactory = new KusamaEventFactory();
        $this->addressFactory = new KusamaAddressFactory();

    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new KusamaBlockchain();
        }

        return self::$staticBlockchain ;
    }
}
