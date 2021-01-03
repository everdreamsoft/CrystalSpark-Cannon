<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class UniqueBlockchainPallet extends SubstrateBlockchain
{
    protected $name = 'uniqueSubstratePallet';
    const NAME = 'uniqueSubstratePallet';
    protected $nameShort = 'unique';
    private static $staticBlockchain;
 

    public function __construct()
    {
        $this->addressFactory = new UniqueAddressFactory();
        $this->contractFactory = new UniqueContractFactory();
        $this->eventFactory = new UniqueEventFactory();
    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new UniqueBlockchainPallet();
        }

        return self::$staticBlockchain ;
    }
}
?>