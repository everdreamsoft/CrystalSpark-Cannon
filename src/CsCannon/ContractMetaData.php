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

    const DISPLAY_TOTAL_SUPPLY = 'totalSupply';
    const DISPLAY_MUTABLE_SUPPLY = 'mutableSupply';
    const DISPLAY_DECIMALS = 'decimals';
    const DISPLAY_INTERFACE = 'interface';

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

    public function getInterface():BlockchainContractStandard{

        return $this->interface ;

    }

    public function setInterface(BlockchainContractStandard $standard){

        $this->contract->createOrUpdateRef('interface',get_class($standard));
        $this->interface = $standard ;
        return $this->interface ;

    }

    public function getTotalSupply(){


    return $this->totalSupply ;

    }

    public function setTotalSupply($totalSupply){

        $this->contract->createOrUpdateRef('totalSupply',$totalSupply);
        $this->totalSupply = $totalSupply ;
        return $this->totalSupply ;

    }


    public function isMutableSupply(){

        return $this->mutableSupply ;

    }

    public function setIsMutableSupply(bool $mutable){

        $this->contract->createOrUpdateRef('mutableSupply',$mutable);
        $this->mutableSupply = $mutable ;
        return $this->mutableSupply ;

    }

    public function getDisplay(){

       $return[static::DISPLAY_TOTAL_SUPPLY] = $this->getTotalSupply();
       $return[static::DISPLAY_MUTABLE_SUPPLY] = $this->getTotalSupply();
       $return[static::DISPLAY_DECIMALS] = $this->getDecimals();
       $return[static::DISPLAY_INTERFACE] = $this->getInterface()->getStandardName();

        return $return ;

    }




    public function refreshData():ContractMetaData{

      // $metadata =  XchainOnBcy::getContractMetaData($this->contract);
       $datasource =  $this->contract->getDataSource();
        $metadata = $datasource->getContractMetaData($this->contract);


        return $metadata ;
    }

}