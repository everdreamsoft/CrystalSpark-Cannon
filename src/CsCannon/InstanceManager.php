<?php
/**
 * Created by PhpStorm.
 * User: shabanshaame
 * Date: 05/12/2019
 * Time: 15:39
 */

namespace CsCannon;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Counterparty\XcpBlockchain;
use CsCannon\Blockchains\Ethereum\EthereumBlockchain;
use SandraCore\Concept;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\System;

class InstanceManager
{

    public function getConfiguration(System $sandra){

        $configFactory = new ConfigurationFactory('',ConfigurationFactory::$file,$sandra);
        $configFactory = $sandra->factoryManager->registerSingletonFactory($configFactory,true);

        $config = $configFactory->getMainConfig();
        print_r($config->dumpMeta());

    }

}



class ConfigurationEntity extends Entity {

    public function getSupportedChains(){

        return $this->getBrotherEntity(SandraManager::getSandra());



    }


}


class ConfigurationFactory extends EntityFactory{

    public static $isa = 'csCannonConfiguration' ;
    public static $file = 'configurationFile' ;
    public const ID = "configId";
    public const SUPPORTED_CHAINS_VERB = "supportedChain";
    public const MAIN_CONFIG = "mainConfig";
    protected static $className = 'CsCannon\ConfigurationEntity' ;
    protected  $generatedEntityClass = 'CsCannon\ConfigurationEntity' ;

    public function getMainConfig():ConfigurationEntity{

       $configEntity = $this->first(self::ID,self::MAIN_CONFIG);

       if (is_null($configEntity)){

           $configEntity = $this->createWithPreset(self::MAIN_CONFIG,new CSCannonDefaultPreset());
       }


       return $configEntity ;


    }

    public function createWithPreset($name,CSCannonConfigPreset $preset):ConfigurationEntity{


        $preset->load();

       $data[self::ID] = $name ;

       $configuration = $this->createNew($data,[self::SUPPORTED_CHAINS_VERB =>$preset->getSupportedBlockchainConcepts($this->system)]);

       $this->getTriplets();

       return $configuration ;



    }


}

abstract class CSCannonConfigPreset {


    /**
     * @var Blockchain[] $supportedBlockchains
     * @return Concept[] supported blockchain concept deducted from the name
     */
    public $supportedBlockchains = array();
    protected  $presetName = 'presetTemplate';

    public function getSupportedBlockchainConcepts(System $system){

        $concepts = array();

        foreach ($this->supportedBlockchains as $blockchain) {

          $system->systemConcept->get($blockchain::NAME) ;
          $concept = $system->conceptFactory->getConceptFromShortnameOrIdOrCreateShortname($blockchain::NAME);
          $concepts[$concept->idConcept] =$system->conceptFactory->getConceptFromShortnameOrIdOrCreateShortname($blockchain::NAME);


                }

        return $concepts ;


    }



}

class CSCannonDefaultPreset extends CSCannonConfigPreset {




   public function load(){

       $this->supportedBlockchains[] = EthereumBlockchain::getStatic();
       $this->supportedBlockchains[] = XcpBlockchain::getStatic();

   }
    protected  $presetName = 'defaultPreset';
    public function getPresetName(){
       return $this->presetName ;
   }



}