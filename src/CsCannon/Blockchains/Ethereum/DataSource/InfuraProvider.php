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
use SandraCore\Concept;

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