<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\FirstOasis;





use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\Ethereum\EthereumEvent;
use CsCannon\Blockchains\Generic\GenericAddressFactory;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use CsCannon\Blockchains\Klaytn\KlaytnAddressFactory;
use CsCannon\Blockchains\Klaytn\KlaytnContractFactory;

class FirstOasisEventFactory extends BlockchainEventFactory
{


    protected static $className = 'CsCannon\Blockchains\FirstOasis\FirstOasis' ; //Update to relevant class



    public function __construct(){

        //Verify data validity

        parent::__construct();

        $this->generatedEntityClass = static::$className ;





    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC')
    {

        $return = parent::populateLocal($limit, $offset, $asc);

        $addressFactory = new FirstOasisAddressFactory();
        $contractFactory = new FirstOasisContractFactory(); //todo should be static

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS,$addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB,$addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT,$contractFactory);


        $this->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);



        return $return ;
    }






}