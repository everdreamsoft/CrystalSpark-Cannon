<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class ERC721 extends EthereumContractStandard
{

    public $specificatorArray = ['tokenId'];




    public function getStandardName()
    {
       return "ERC721";
    }


}