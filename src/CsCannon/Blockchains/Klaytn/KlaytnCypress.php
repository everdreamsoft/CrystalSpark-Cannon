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

class KlaytnCypress extends RpcProvider
{
    public const HOST_URL = 'https://api.cypress.klaytn.net:8651/' ;


    public function getHostUrl($apiKey = null)
    {
        return self::HOST_URL;
    }

    public function getBalance(BlockchainContract $contract, BlockchainAddress $address, BlockchainContractStandard $standard){



        $cmd = "node public/caver/getBalance.js --contract=".$contract->get(BlockchainContractFactory::MAIN_IDENTIFIER)." --target=".$address->getAddress()." --node=".$this->getHostUrl();

        return  exec($cmd);

    }

    public function transform(Concept $concept, $value){


        //If a specific chain provider need to transform data

        $sandra = $concept->system;
        $tixIdConcept = $sandra->conceptFactory->getConceptFromShortnameOrId(Blockchain::$txidConceptName);

        if ($tixIdConcept->idConcept == $concept->idConcept){

            return "0x$value";
        }

        return $value ;


    }

    public function ownerOf(BlockchainContract $contract, $tokenId, BlockchainContractStandard $standard){



        $cmd = "node public/caver/ownerOf.js --contract=".$contract->get(BlockchainContractFactory::MAIN_IDENTIFIER)." --tokenId=$tokenId --node=".$this->getHostUrl();
        return  exec($cmd);

    }


    public function getBlockchain():Blockchain
    {
        return new KlaytnBlockchain();
    }
}