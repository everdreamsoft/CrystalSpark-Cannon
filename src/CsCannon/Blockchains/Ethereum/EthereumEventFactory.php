<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;


use CsCannon\Blockchains\BlockchainEventFactory;

class EthereumEventFactory extends BlockchainEventFactory
{


    protected static $className = 'CsCannon\Blockchains\Ethereum\EthereumEvent'; //Update to relevant class


    public function __construct()
    {

        //Verify data validity

        parent::__construct();

        $this->generatedEntityClass = static::$className;

        $this->setFilter(self::ON_BLOCKCHAIN_EVENT, EthereumBlockchain::NAME); //Update relevant chain name

        $this->addressFactory = new EthereumAddressFactory();
        $this->contractFactory = new EthereumContractFactory(); //todo should be static


    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC', $sortByRef = null, $numberSort = false)
    {

        $return = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        //$return = parent::populateFromParent($limit, $offset, $asc, $sortByRef, $numberSort);

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS, $this->addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB, $this->addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT, $this->contractFactory);

        //$blockFactory = new BlockchainBlockFactory(EthereumBlockchain::getStatic());
        //  $this->joinFactory(self::EVENT_BLOCK, $blockFactory);
        $this->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);
        //$this->getTriplets();

        return $return;

    }


}
