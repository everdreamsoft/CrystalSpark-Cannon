<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Ethereum;



use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainAddressFactory;
use CsCannon\Blockchains\BlockchainContract;
use SandraCore\ForeignEntityAdapter;

class EthereumContract extends BlockchainContract
{

    protected static $isa = 'ethContract';

    protected static  $className = 'CsCannon\Blockchains\Ethereum\EthereumContract' ;


    public function resolveMetaData ($tokenId = null){


        $address = $this->get(BlockchainAddressFactory::ADDRESS_SHORTNAME);

        $assetCollectionFactory = AssetCollectionFactory::getStaticCollection() ;
        $collectionEntity = $assetCollectionFactory->get($address);

        if($collectionEntity instanceof AssetCollection) {

            $hostName = $_SERVER['HTTP_HOST'];
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';





            return array("image"=>"$protocol$hostName/api/v1/$address/image/$tokenId");
            return $collectionEntity->getDefaultDisplay();
        }

        return 'unknownCollection';



    }



    public function getBlockchain():Blockchain
    {
        return EthereumBlockchain::getStatic();
    }







}