<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ordinal;

use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainEventFactory;

class OrdinalEventFactory extends BlockchainEventFactory
{

    protected static $className = 'CsCannon\Blockchains\Ordinal\OrdinalEvent' ; //Update to relevant class

    public function __construct(){

        //Verify data validity
        parent::__construct();
        $this->generatedEntityClass = static::$className ;
        $this->setFilter(self::ON_BLOCKCHAIN_EVENT,OrdinalBlockchain::NAME);
        $this->addressFactory = new OrdinalAddressFactory();
        $this->contractFactory = new OrdinalContractFactory();

    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC',$sortByRef = null, $numberSort = false)
    {
        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS,$this->addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB,$this->addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT,$this->contractFactory);

        $blockFactory = new BlockchainBlockFactory(OrdinalBlockchain::getStatic());
        $this->joinFactory(self::EVENT_BLOCK,$blockFactory);

        $this->joinPopulate();
        $this->populateBrotherEntities(self::EVENT_CONTRACT);
        $this->getTriplets();

        return $return ;
    }

}
