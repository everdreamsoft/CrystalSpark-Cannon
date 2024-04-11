<?php
/**
 * Created by PhpStorm.
 * User: Ranjit
 * Date: 12.03.2021
 * Time: 15:00
 */

namespace CsCannon\Blockchains\Polygon;

use CsCannon\Blockchains\BlockchainEventFactory;


class PolygonEventFactory extends BlockchainEventFactory
{

    protected static $className = 'CsCannon\Blockchains\Polygon\PolygonEvent';

    public function __construct()
    {
        parent::__construct();
        $this->generatedEntityClass = static::$className;
        $this->setFilter(self::ON_BLOCKCHAIN_EVENT, PolygonBlockchain::NAME); //Update relevant chain name
    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC', $sortByRef = null, $numberSort = false)
    {

        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);

        $addressFactory = new PolygonAddressFactory();
        $contractFactory = new PolygonContractFactory();
        
        $this->joinFactory(self::EVENT_SOURCE_ADDRESS, $addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB, $addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT, $contractFactory);

        $this->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);

        return $return;
    }


}
