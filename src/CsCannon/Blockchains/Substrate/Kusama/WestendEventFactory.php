<?php

namespace CsCannon\Blockchains\Substrate\Kusama;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Substrate\RMRK\RmrkContractFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class WestendEventFactory extends SubstrateEventFactory
{
    protected static $className = KusamaEvent::class;

    public function __construct(){

        parent::__construct();

        $this->generatedEntityClass = static::$className;

        $this->setFilter(self::ON_BLOCKCHAIN_EVENT, WestendBlockchain::NAME);

        $this->addressFactory = new WestendAddressFactory();
        $this->contractFactory = new RmrkContractFactory();
    }


}
?>