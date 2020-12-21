<?php

namespace CsCannon\Blockchains\Substrate;


use CsCannon\Blockchains\BlockchainTokenFactory;

class SubstrateTokenFactory extends BlockchainTokenFactory
{
    protected static $isa = "SubstrateToken";
    protected static $file = "SubstrateTokenFile";
    protected static $className = SubstrateToken::class;

    public function __construct(){
        parent::__construct();
    }
}