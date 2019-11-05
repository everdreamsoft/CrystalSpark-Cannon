<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;



abstract class Blockchain
{

   protected $name ;
   public static $blockchainConceptName = 'blockchain';
   public static $txidConceptName = 'txHash' ;
   public static $provider_opensea_enventId = 'openSeaId' ;

    const NAME = 'genericBlockchain';
    const NETWORK_NAME = 'main';

   public  $addressFactory ;
    public  $contractFactory ;
    public  $eventFactory ;
    public  $blockFactory ;

    public function getAddressFactory()
    {

        return $this->addressFactory;

    }

    public function getContractFactory()
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




}