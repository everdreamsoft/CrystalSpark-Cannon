<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 14:02
 */

namespace CsCannon\Blockchains\Ethereum\DataSource;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\RpcProvider;

class InfuraProvider extends RpcProvider
{
    public const HOST_URL = 'https://mainnet.infura.io/v3/' ;

    public function getHostUrl()
    {
        return self::HOST_URL.$this->apiKey ;
    }

    public function getBlockchain(): Blockchain
    {
      return  new EthereumBlockchain();
    }
}