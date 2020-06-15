<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 14:02
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\DataSource\InfuraProvider;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;

class MaticProvider extends InfuraProvider
{
    public const HOST_URL = 'https://testnetv3.matic.network' ;
    public $requestPerSecond = 10;

    public function getHostUrl()
    {
        return self::HOST_URL ;
    }

    public function getBlockchain(): Blockchain
    {
      return  new MaticBlockchain();
    }


    public function ownerOf(BlockchainContract $contract, $tokenId, BlockchainContractStandard $standard){



        $cmd = "node CSNotary/NotaryExecutor.js --contract=".$contract->get(BlockchainContractFactory::MAIN_IDENTIFIER).
            ' --command="ownerOf"'.
            ' --tokenPath="{  \"tokenId\": '.$tokenId.',   \"message\": 2 }"'.
            ' --chain="matic"';
        return  exec($cmd);

    }


}