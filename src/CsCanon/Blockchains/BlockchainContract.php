<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains;




use App\Asset;
use App\AssetCollectionFactory;
use App\AssetFactory;
use App\Token;
use CsCanon\BlockchainAddress;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

abstract class  BlockchainContract extends BlockchainAddress
{


    public function resolveMetaData (){


        return 'helloMeta';

    }








}