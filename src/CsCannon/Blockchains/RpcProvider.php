<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 13:39
 */

namespace CsCannon\Blockchains;


use SandraCore\Concept;

abstract class RpcProvider
{

    public $apiKey;
    public $requestPerSecond = -1;



    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;


    }

    public function getRequestPerSecond()
    {
        return $this->requestPerSecond ;


    }

    public abstract function getHostUrl();
    public abstract function getBlockchain():Blockchain;
    public abstract function transform(Concept $concept, $value);






}