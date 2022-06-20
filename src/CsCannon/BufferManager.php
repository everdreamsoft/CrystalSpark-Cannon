<?php


namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;

class BufferManager
{

    private static $assetFactoryArray ;
    private TokenPathToAssetFactory $tokenToAssetFactory;
    private AssetFactory $assetFactory;



    /**
     *
     * preload an asset factory with blockchain tokens on a contract
     *

     * @param BlockchainContract $contract
     * @param BlockchainContractStandard[] $specifiers
     */
    public function loadAssetFactoryFromSpecifiers(BlockchainContract $contract, array $specifiers){

        $this->tokenToAssetFactory = new TokenPathToAssetFactory($contract->system);
        $array = [];
        foreach ( $specifiers as $specifier){
            $array[] = $specifier->subjectConcept->idConcept ;
        }
        $this->tokenToAssetFactory->conceptManager->conceptArray = $array ;
        $this->tokenToAssetFactory->populateLocal();
        $this->tokenToAssetFactory->populateBrotherEntities();

        //$tokenToAssetFactory->createViewTable('Tokens');


        $this->assetFactory = new AssetFactory();
        $this->tokenToAssetFactory->joinFactory($contract,$this->assetFactory);
        $this->tokenToAssetFactory->joinPopulate();


        return $this->assetFactory ;

    }

    public function getBufferedTokenToAsset():TokenPathToAssetFactory{

        return $this->tokenToAssetFactory ;


    }

    public function getBufferedAssetFactory():AssetFactory{

        return $this->assetFactory ;


    }


}