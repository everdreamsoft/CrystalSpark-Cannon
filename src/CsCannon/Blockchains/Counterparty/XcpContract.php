<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;


use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainTokenFactory;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;

class XcpContract extends BlockchainContract
{

    public static $isa = 'xcpContract';
    public static $file = 'blockchainContractFile';
    public static $className = 'CsCannon\Blockchains\Counterparty\XcpContract';


    private float $xcpPrice = 0;
    private float $btcPrice = 0;

    public function resolveMetaData()
    {

        $collectionsArray = array();
        /** @var Asset $assetEntity */
        $assetArray = $this->getJoinedEntities(BlockchainTokenFactory::$joinAssetVerb);

        if (is_array($assetArray)) {
            foreach ($assetArray as $assetEntity) {

                $collections = $assetEntity->getJoinedEntities(AssetFactory::$collectionJoinVerb);

                if (is_array($collections)) {

                    foreach ($collections as $collectionEntity) {

                        /** @var AssetCollection $collectionEntity */
                        $collectionsArray[] = $collectionEntity;

                    }

                }


            }
        }


        if (is_array($assetArray)) {

            $firstAsset = reset($assetArray);
            return $firstAsset->getDisplayableCollection($collectionsArray);

        } else return array('image' => 'https://static.cryptorival.com/imgs/coins/counterparty.png');


        return array();

    }

    public function getStandard(): ?BlockchainContractStandard
    {


        parent::getStandard();

        return CounterpartyAsset::init();
    }


    function getBlockchain(): Blockchain
    {
        return XcpBlockchain::getStatic();
    }

    function setDivisible()
    {
        return parent::setDivisibility(8);
    }

    function setXcpPrice(float $val)
    {
        $this->xcpPrice = $val;
    }

    function getXcpPrice(): float
    {
        return $this->xcpPrice;
    }

    function setBtcPrice(float $val)
    {
        $this->btcPrice = $val;
    }

    function getBtcPrice(): float
    {
        return $this->btcPrice;
    }

}
