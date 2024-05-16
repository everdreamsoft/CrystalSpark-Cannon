<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 14:02
 */

namespace CsCannon\Blockchains\Polygon\DataSource;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Polygon\PolygonBlockchain;
use CsCannon\Blockchains\RpcProvider;
use SandraCore\Concept;

class InfuraProvider extends RpcProvider
{
    const HOST_URL = 'https://polygon-mainnet.infura.io/v3/';
    public $requestPerSecond = 1;

    public function getHostUrl()
    {
        return static::HOST_URL . $this->apiKey;
    }

    public function getBlockchain(): Blockchain
    {
        return new PolygonBlockchain();
    }

    public function transform(Concept $concept, $value)
    {
        //If a specific chain provider need to transform data

        $sandra = $concept->system;
        $tixIdConcept = $sandra->conceptFactory->getConceptFromShortnameOrId(Blockchain::$txidConceptName);

        if ($tixIdConcept->idConcept == $concept->idConcept) {
            return "0x$value";
        }

        return $value;

    }
}
