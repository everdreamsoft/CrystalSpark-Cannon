<?php

namespace CsCannon\Blockchains\Substrate;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEventFactory;

class SubstrateEventFactory extends BlockchainEventFactory
{
    protected static $className = SubstrateEvent::class;

    public function __construct(){

        parent::__construct();

        $this->generatedEntityClass = static::$className;

        //$this->setFilter(self::ON_BLOCKCHAIN_EVENT, SubstrateBlockchain::NAME);

        $this->addressFactory = new SubstrateAddressFactory();
        $this->contractFactory = new SubstrateContractFactory();
    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC')
    {
        $return = parent::populateLocal($limit, $offset, $asc);

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS, $this->addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB, $this->addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT, $this->contractFactory);

        $this->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);

        return $return;
    }
}
?>