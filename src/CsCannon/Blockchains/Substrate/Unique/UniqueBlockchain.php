<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class UniqueBlockchain extends SubstrateBlockchain
{
    protected $name = 'uniqueBlockchain';
    const NAME = 'uniqueBlockchain';
    protected $nameShort = 'unique';
    private static $staticBlockchain;
 

    public function __construct()
    {

        $this->contractFactory = new RmrkContractFactory();
        $this->eventFactory = new UniqueEventFactory();
        $this->addressFactory = new UniqueAddressFactory();


    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new UniqueBlockchain();
        }

        return self::$staticBlockchain ;
    }
}
