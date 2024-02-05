<?php


namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;

class BufferManager
{

    private  ?AssetFactory $assetFactoryForContracts = null  ;
    private TokenPathToAssetFactory $tokenToAssetFactory;
    private AssetFactory $assetFactory;






    /**
     *
     * preload an asset factory with blockchain tokens on a contract
     *

     * @param BlockchainContract $contract
     * @param BlockchainContractStandard[] $specifiers
     */
    public function loadAssetFactoryFromSpecifiers(array $specifiers){

        $this->tokenToAssetFactory = new TokenPathToAssetFactory(SandraManager::getSandra());
        $array = [];
        foreach ( $specifiers as $specifier){

            $array[] = $specifier->getDisplayStructure() ;

        }
        $array = array_unique($array);

        $this->tokenToAssetFactory->conceptArray = $array ;

        $this->tokenToAssetFactory->populateFromSearchResults($array);
       // $this->tokenToAssetFactory->populateBrotherEntities();

        //$tokenToAssetFactory->createViewTable('Tokens');




    }

    public function getBufferedTokenToAsset(BlockchainContract $contract):TokenPathToAssetFactory{

        if (!isset($this->tokenToAssetFactory->joinedFactoryArray[$contract->subjectConcept->idConcept])) {

            $this->getBufferedAssetFactory($contract);

        }

        return $this->tokenToAssetFactory ;


    }

    public function getBufferedAssetFactory(BlockchainContract $contract):AssetFactory{

        if (isset($this->tokenToAssetFactory->joinedFactoryArray[$contract->subjectConcept->idConcept])) {

           return $this->tokenToAssetFactory->joinedFactoryArray[$contract->subjectConcept->idConcept];

        }


        $this->assetFactory = new AssetFactory();
        $this->tokenToAssetFactory->joinFactory($contract,$this->assetFactory);
        $this->tokenToAssetFactory->joinPopulate();

        return $this->tokenToAssetFactory->joinedFactoryArray[$contract->subjectConcept->idConcept];


    }

    public function loadAssetsFromContracts(array $contracts) {
        if (empty($contracts)) return;

        $this->assetFactoryForContracts = new AssetFactory();
        $this->assetFactoryForContracts->setFilter(0, $contracts);
        $this->assetFactoryForContracts->populateLocal();
        $this->assetFactoryForContracts->getTriplets();
        $this->assetFactoryForContracts->populateBrotherEntities(AssetFactory::$tokenJoinVerb);

    }

    public function hasDirectContractToAssets():?AssetFactory
    {
        if ($this->assetFactoryForContracts) return $this->assetFactoryForContracts ;

        return null;

    }




}
