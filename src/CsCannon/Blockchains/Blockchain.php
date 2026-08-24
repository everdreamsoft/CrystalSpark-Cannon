<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;



use CsCannon\Tools\BlockchainOrderProcess;

abstract class Blockchain
{

   protected $name ;
   public static $blockchainConceptName = 'blockchain';

   /**
    * Opt-in: when true, block entities are namespaced per chain
    * (<NAME>Bloc / <NAME>BlocFile, e.g. counterpartyBloc) instead of the
    * legacy shared blockchainBloc namespace — blockIndex collides across
    * chains in a multi-chain datagraph otherwise. Off by default so existing
    * datagraphs keep resolving their blocks; enable it on fresh datagraphs
    * (or after migrating the is_a / contained_in triplets of legacy blocks).
    */
   public static $perChainBlockConcepts = false;

   public static function blockConceptBase(): string
   {
       return static::$perChainBlockConcepts ? static::NAME : static::$blockchainConceptName;
   }
   public static $txidConceptName = 'txHash' ;
   public static $provider_opensea_enventId = 'openSeaId' ;

    public  $mainSourceCurrencyTicker = 'NULL' ;

    const NAME = 'genericBlockchain';
    const NETWORK_NAME = 'main';

   public  $addressFactory ;
    public  $contractFactory ;
    public  $eventFactory ;
    public  $blockFactory ;
    public $orderProcess;
    public static $network = array("null"=>array("explorer_tx"=>'null'));

    /**
     * @return BlockchainAddressFactory
     */
    public function getAddressFactory()
    {

        return $this->addressFactory;

    }

    public function getContractFactory():BlockchainContractFactory
    {

        return $this->contractFactory;

    }

    public function getEventFactory():BlockchainEventFactory
    {

        return $this->eventFactory;

    }

    public function getBlockFactory()
    {

       return new BlockchainBlockFactory($this);

    }

    public function getOrderProcess(): BlockchainOrderProcess
    {
        return $this->orderProcess;
    }

    public function getMainCurrencyTicker():string
    {

        return $this->mainSourceCurrencyTicker ;

    }

    public static function getNetworkData($network,$data)
    {

        if (isset(static::$network[$network][$data])){

            return static::$network[$network][$data] ;
        }

        return null ;


    }





}