<?php


namespace CsCannon\Tools;


use CsCannon\Blockchains\Blockchain;

class BlockchainOrderProcess
{

    public $blockchain;

    public function __construct(Blockchain $blockchain){
        $this->blockchain = $blockchain;
    }


}