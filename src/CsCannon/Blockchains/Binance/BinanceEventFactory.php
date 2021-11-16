<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15.11.2021
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Binance;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Binance\BinanceEvent;

class BinanceEventFactory extends BlockchainEventFactory
{

    protected static $className = 'CsCannon\Blockchains\Binance\BinanceEvent' ; //Update to relevant class

    public function __construct(){

        //Verify data validity
        parent::__construct();
        $this->generatedEntityClass = static::$className ;
        $this->setFilter(self::ON_BLOCKCHAIN_EVENT,BinanceBlockchain::NAME); //Update relevant chain name
        $this->addressFactory = new BinanceAddressFactory();
        $this->contractFactory = new BinanceContractFactory(); //todo should be static

    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC',$sortByRef = null, $numberSort = false)
    {
        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        $this->joinFactory(self::EVENT_SOURCE_ADDRESS,$this->addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB,$this->addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT,$this->contractFactory);
        $this->joinPopulate();
        $this->populateBrotherEntities(self::EVENT_CONTRACT);
        return $return ;
    }

}