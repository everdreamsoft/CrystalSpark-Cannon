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



    public function __construct($apiKey = null)
    {


    }

    public abstract function getHostUrl();
    public abstract function getBlockchain();

}