<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Ethereum;



use App\Blockchains\Blockchain;


class EthereumBlockchain extends Blockchain
{

   protected $name = 'ethereum';
   const NAME = 'ethereum';
    protected $nameShort = 'eth';

    public function __construct()
    {

        $this->addressFactory = new EthereumAddressFactory();
        $this->contractFactory = new EthereumContractFactory();
        $this->eventFactory = new EthereumEventFactory();

    }








}