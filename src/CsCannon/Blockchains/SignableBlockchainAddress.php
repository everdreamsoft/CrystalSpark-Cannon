<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 16.06.20
 * Time: 16:02
 */

namespace CsCannon\Blockchains;


class SignableBlockchainAddress
{
    public $address ;
    private $privateKey ;
    
    public function __construct(BlockchainAddress $address,$privateKey)
    {

        $this->address = $address ;
        $this->privateKey = $privateKey ;
        
    }

    public function getPrivateKey()
    {

        return $this->privateKey ;


    }

}