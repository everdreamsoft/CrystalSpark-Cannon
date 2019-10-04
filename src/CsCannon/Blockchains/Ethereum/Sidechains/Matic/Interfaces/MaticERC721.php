<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 29.09.2019
 * Time: 10:50
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic\Interfaces;


use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;

class MaticERC721 extends ERC721
{

    public function getStandardName()
    {
        return "Matic-ERC721";
    }


}