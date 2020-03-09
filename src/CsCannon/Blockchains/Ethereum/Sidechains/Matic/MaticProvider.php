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
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;

class MaticProvider extends InfuraProvider
{
    public const HOST_URL = 'https://static.matic.network/network/testnet/v3' ;

    public function getHostUrl()
    {
        return self::HOST_URL ;
    }

    public function getBlockchain(): Blockchain
    {
      return  new MaticBlockchain();
    }


}