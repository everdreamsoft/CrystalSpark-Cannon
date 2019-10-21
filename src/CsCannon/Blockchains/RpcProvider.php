<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-10-21
 * Time: 13:39
 */

namespace CsCannon\Blockchains;


abstract class RpcProvider
{

    public $apiKey;



    public function __construct($apiKey = null)
    {
        $this->apiKey = $apiKey;


    }

    public abstract function getHostUrl();
    public abstract function getBlockchain():Blockchain;

}