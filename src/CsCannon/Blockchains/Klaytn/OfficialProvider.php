<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 13:43
 */

namespace CsCannon\Blockchains\Klaytn;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnEventFactory;
use CsCannon\Blockchains\RpcProvider;

class OfficialProvider extends RpcProvider
{
    public const HOST_URL = 'https://api.baobab.klaytn.net:8651/' ;


    public function getHostUrl($apiKey = null)
    {
        return self::HOST_URL;
    }

    public function getBalance(BlockchainContract $contract, BlockchainAddress, $address, BlockchainContractStandard $standard)
    {

        return 1 ;



    }


    public function getBlockchain():Blockchain
    {
        return new KlaytnBlockchain();
    }
}