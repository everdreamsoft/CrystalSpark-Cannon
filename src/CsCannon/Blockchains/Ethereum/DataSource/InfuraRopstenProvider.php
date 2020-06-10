<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 14:02
 */

namespace CsCannon\Blockchains\Ethereum\DataSource;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use CsCannon\Blockchains\Ethereum\RopstenEthereumBlockchain;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;

class InfuraRopstenProvider extends InfuraProvider
{
    public const HOST_URL = 'https://ropsten.infura.io/v3/' ;




    public function getBlockchain(): Blockchain
    {
      return  new RopstenEthereumBlockchain();
    }

    public function ownerOf(BlockchainContract $contract, $tokenId, BlockchainContractStandard $standard){



        $cmd = "node CSNotary/NotaryExecutor.js --contract=".$contract->get(BlockchainContractFactory::MAIN_IDENTIFIER).
            ' --command="ownerOf"'.
            ' --tokenPath="{  \"tokenId\": '.$tokenId.',   \"message\": 2 }"'.
            ' --chain="ethereum"'.
            ' --network="ropsten"';
        return  exec($cmd);

    }


}