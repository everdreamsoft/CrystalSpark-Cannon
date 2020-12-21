<?php

namespace CsCannon\Blockchains\Substrate\Unique;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Substrate\SubstrateAddressFactory;
use CsCannon\Blockchains\Substrate\SubstrateEventFactory;

class UniqueEventFactory extends SubstrateEventFactory
{
    protected static $className = UniqueEventFactory::class;

    public function __construct(){

        parent::__construct();

        $this->generatedEntityClass = static::$className;

        $this->setFilter(self::ON_BLOCKCHAIN_EVENT, UniqueBlockchain::NAME);

        $this->addressFactory = new UniqueAddressFactory();
        $this->contractFactory = new UniqueContractFactory();
    }


}
?>