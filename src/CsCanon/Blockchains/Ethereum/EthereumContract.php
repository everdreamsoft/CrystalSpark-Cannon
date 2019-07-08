<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace App\Blockchains\Ethereum;



use App\AssetCollection;
use App\AssetCollectionFactory;
use App\Blockchains\Bitcoin\BitcoinAddress;
use App\Blockchains\BlockchainAddress;
use App\Blockchains\BlockchainAddressFactory;
use App\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;

class EthereumContract extends EthereumAddress
{

    protected static $isa = 'ethContract';
    protected static $file = 'blockchainContractFile';
    protected static  $className = 'App\Blockchains\Ethereum\EthereumContract' ;


    public function resolveMetaData ($tokenId = null){


        $address = $this->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        $assetCollectionFactory = AssetCollectionFactory::getStaticCollection() ;
        $collectionEntity = $assetCollectionFactory->get($address);

        if($collectionEntity instanceof AssetCollection) {


          return array("image"=>"https://apidev.bitcrystals.com/api/v1/$address/image/$tokenId");
            return $collectionEntity->getDefaultDisplay();
        }

        return 'unknownCollection';



    }







}