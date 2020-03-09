<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 14:02
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Ethereum\DataSource\InfuraProvider;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;

class MaticProvider extends InfuraProvider
{
    public const HOST_URL = 'https://testnetv3.matic.network' ;

    public function getHostUrl()
    {
        return self::HOST_URL ;
    }

    public function getBlockchain(): Blockchain
    {
      return  new MaticBlockchain();
    }


}