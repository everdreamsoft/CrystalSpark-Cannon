<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains;



abstract class Blockchain
{

   protected $name ;
   public static $blockchainConceptName = 'blockchain';
   public static $txidConceptName = 'txHash' ;
   public static $provider_opensea_enventId = 'openSeaId' ;

    const NAME = 'genericBlockchain';

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

    public function getEventFactory()
    {

        return $this->eventFactory;

    }

    public function getBlockFactory()
    {

       return new BlockchainBlockFactory($this);

    }




}