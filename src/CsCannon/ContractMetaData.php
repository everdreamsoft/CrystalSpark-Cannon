<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 24.11.20
 * Time: 11:39
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;

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
    private $totalSupply;
    private $mutableSupply;
    private $interface;

    public function __construct(BlockchainContract $contract){
        
        
        $this->contract = $contract ;
        $this->decimals = $contract->get('decimals');
        $this->totalSupply = $contract->get('totalSupply');
        $this->mutableSupply = $contract->get('mutableSupply');
        $this->interface = $contract->get('interface');
        
    }

    public function getDecimals(){

    return $this->decimals ;

    }

    public function setDecimals($decimals){

        $this->contract->createOrUpdateRef('decimals',$decimals);
        return $this->decimals = $decimals ;

    }

    public function getInterface(){

        return $this->interface ;

    }

    public function setInterface(BlockchainContractStandard $standard){

        $this->contract->createOrUpdateRef('interface',get_class($standard));
        return $this->interface ;

    }

    public function getTotalSupply(){


    return $this->totalSupply ;

    }

    public function setTotalSupply($totalSupply){

        $this->contract->createOrUpdateRef('totalSupply',$totalSupply);
        return $this->totalSupply ;

    }


    public function isMutableSupply(){

        return $this->mutableSupply ;

    }

    public function setIsMutableSupply(bool $mutable){

        $this->contract->createOrUpdateRef('mutableSupply',$mutable);
        return $this->mutableSupply ;

    }




    public function refreshData():ContractMetaData{

       $metadata =  XchainOnBcy::getContractMetaData($this->contract);


        return $this ;
    }

}