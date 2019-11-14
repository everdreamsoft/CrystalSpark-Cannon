<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-10
 * Time: 10:22
 */

namespace CsCannon\AssetSolvers;


use CsCannon\Asset;
use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\XcpAddressFactory;
use CsCannon\Blockchains\Counterparty\XcpContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\MetadataSolverFactory;
use CsCannon\Orb;
use CsCannon\SandraManager;
use InnateSkills\LearnFromWeb\LearnFromWeb;
use SandraCore\EntityFactory;
use SandraCore\ForeignConcept;
use SandraCore\ForeignEntityAdapter;
use SandraCore\System;

class PathPredictableSolver extends AssetSolver
{

    private static $assetInCollections ;
    public  $additionalSolverParam ;



    public static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract): ?array{


        $solvers = $assetCollection->getSolvers();
        $solverData = $assetCollection->getBrotherEntity(AssetCollectionFactory::METADATASOLVER_VERB,self::getEntity());
        $return = array();

        //get the correct solver
        foreach ($solvers ? $solvers : array() as $pathSolverEntity){


            if ($pathSolverEntity instanceof PathPredictableSolver){

                $foreignAssetFactory = new ForeignEntityAdapter(null,null,SandraManager::getSandra());
                $assetConcept = new ForeignConcept("".$specifier->getDisplayStructure(),SandraManager::getSandra());

                $imgUrl =  $solverData->get(Asset::IMAGE_URL);
                $metadataUrl =  $solverData->get(Asset::METADATA_URL);

                $finalImage = $imgUrl ;
                $finalMetaData = $metadataUrl ;

                //now we replace the strings
                foreach($specifier->specificatorData ? $specifier->specificatorData : array() as $data => $dataValue ){

                    $finalImage = str_replace('{{'.$data.'}}',"$dataValue",$finalImage);
                    $finalMetaData = str_replace('{{'.$data.'}}',"$dataValue",$finalMetaData);

                }

                $data = array(AssetFactory::IMAGE_URL=>$finalImage,
                    AssetFactory::METADATA_URL=>$finalMetaData,
                    AssetFactory::ID=>$specifier->getDisplayStructure()
                );

                $return[] = new Asset($assetConcept,$data,$foreignAssetFactory,$data,AssetFactory::$isa,AssetFactory::$file,SandraManager::getSandra());



            }

        }

        return $return;



    }


    protected static function updateSolver()
    {
        return ;
    }







    public static function getEntity($imagePath=null,$metadataPath=null):AssetSolver {




         $entity = parent::getEntity();
         /** @var PathPredictableSolver $entity */
         $entity->setAdditionalParam([Asset::IMAGE_URL=>$imagePath,Asset::METADATA_URL=>$metadataPath]);

        return $entity;


    }
}