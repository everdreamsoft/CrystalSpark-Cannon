<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;






abstract class  BlockchainContract extends BlockchainAddress
{


    abstract public function resolveMetaData ($tokenId = null);








}