<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains;




use CsCannon\Asset;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Token;
use CsCannon\BlockchainAddress;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

abstract class  BlockchainContract extends BlockchainAddress
{


    public function resolveMetaData (){


        return 'helloMeta';

    }








}