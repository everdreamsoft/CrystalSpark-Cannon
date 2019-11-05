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
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;
use CsCannon\Blockchains\Klaytn\KlaytnEventFactory;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class OfficialProvider extends RpcProvider
{
    public const HOST_URL = 'https://api.baobab.klaytn.net:8651/' ;


    public function getHostUrl($apiKey = null)
    {
        return self::HOST_URL;
    }

    public function getBalance(BlockchainContract $contract, BlockchainAddress $address, BlockchainContractStandard $standard){



        $cmd = "node public/caver/index.js --contract=".$contract->get(BlockchainContractFactory::MAIN_IDENTIFIER)." --target=".$address->getAddress()."";
        return  exec($cmd);

    }


    public function getBlockchain():Blockchain
    {
        return new KlaytnBlockchain();
    }

    public function transform(Concept $concept, $value)
    {
        //If a specific chain provider need to transform data

        $sandra = $concept->system;
        $tixIdConcept = $sandra->conceptFactory->getConceptFromShortnameOrId(Blockchain::$txidConceptName);

        if ($tixIdConcept->idConcept == $concept->idConcept){

            return "0x$value";
        }

        return $value ;

    }
}