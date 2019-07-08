<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Ethereum;





use App\Blockchains\Blockchain;
use App\Blockchains\BlockchainEventFactory;
use App\Blockchains\Ethereum\EthereumEvent;

class EthereumEventFactory extends BlockchainEventFactory
{


    protected static $className = 'App\Blockchains\Ethereum\EthereumEvent' ;



    public function __construct(){

        //Verify data validity


        parent::__construct();

        $this->generatedEntityClass = static::$className ;

        $this->setFilter(self::ON_BLOCKCHAIN_EVENT,EthereumBlockchain::NAME);





    }

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC')
    {



        $return = parent::populateLocal($limit, $offset, $asc);



        $addressFactory = new EthereumAddressFactory();
        $contractFactory = new EthereumContractFactory();

        $this->joinFactory(self::EVENT_SOURCE_ADDRESS,$addressFactory);
        $this->joinFactory(self::EVENT_DESTINATION_VERB,$addressFactory);
        $this->joinFactory(self::EVENT_CONTRACT,$contractFactory);


        $this->joinPopulate();

        $this->populateBrotherEntities(self::EVENT_CONTRACT);



        return $return ;
    }












}