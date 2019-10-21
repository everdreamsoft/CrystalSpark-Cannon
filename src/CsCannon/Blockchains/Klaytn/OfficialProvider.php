<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 13:43
 */

namespace CsCannon\Blockchains\Klaytn;


use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\RpcProvider;

class OfficialProvider extends RpcProvider
{
    public const HOST_URL = 'https://api.baobab.klaytn.net:8651/' ;


    public function getHostUrl($apiKey = null)
    {
        return self::HOST_URL;
    }

    public function getBlockchain()
    {
        return new EthereumBlockchain();
    }
}