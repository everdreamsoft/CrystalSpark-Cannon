<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateBlockchain;
use CsCannon\Blockchains\Substrate\SubstrateContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class KusamaBlockchainPallet extends SubstrateBlockchain
{
    protected $name = 'kusamaSubstratePallet';
    const NAME = 'kusamaSubstratePallet';
    protected $nameShort = 'kusama';
    private static $staticBlockchain;
 

    public function __construct()
    {
        $this->addressFactory = new KusamaAddressFactory();
        $this->contractFactory = new KusamaContractFactory();
        $this->eventFactory = new KusamaEventFactory();
    }

    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new KusamaBlockchainPallet();
        }

        return self::$staticBlockchain ;
    }
}
?>