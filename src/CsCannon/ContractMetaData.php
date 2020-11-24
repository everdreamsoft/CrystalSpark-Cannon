<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 24.11.20
 * Time: 11:39
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;

class ContractMetaData
{

    /**
     * @var BlockchainContract
     */
    private $contract;
    /**
     * @var |null
     */
    private $decimals;

    public function __construct(BlockchainContract $contract){
        
        
        $this->contract = $contract ;
        $this->decimals = $contract->get('decimals');
        
    }

    public function getDecimals(){



    }

    public function refreshData(){



    }

}