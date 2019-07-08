<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;



use CsCannon\Blockchains\Bitcoin\BtcBlockchain;


 class XcpBlockchain extends BtcBlockchain
{

   const NAME = 'counterparty';


   public function __construct()
   {

       $this->addressFactory = new XcpAddressFactory();
       $this->eventFactory = new XcpEventFactory();
       $this->contractFactory = new XcpContractFactory();

   }




 }