<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum\Sidechains\Matic;




use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;

use CsCannon\Blockchains\Ethereum\DataSource\BlockscoutAPI;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\EthereumAddress;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Ethereum\MaticBlockchain;


class MaticAddress extends EthereumAddress //update relevant parent
{

    protected static $isa = 'ethAddress';
    protected static $file = 'ethAddressFile';
    protected static  $className = MaticAddress::class ; //Update relevant path





    public function getDefaultDataSource(): BlockchainDataSource
    {


        return  new BlockscoutAPI();
    }


    public function getBlockchain(): Blockchain
    {
        return \CsCannon\Blockchains\Ethereum\Sidechains\Matic\MaticBlockchain::getStatic();
    }




}