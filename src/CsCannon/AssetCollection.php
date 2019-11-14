<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

use CsCannon\AssetSolvers\AssetSolver;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainToken;
use CsCannon\Blockchains\BlockchainTokenFactory;
use SandraCore\System;

class AssetCollection extends \SandraCore\Entity
{

    public $id = 'collectionId' ;
    public $name = 'name';
    public $imageUrl = 'imageUrl';
    public $description = 'description';

    private $displayArray = array();

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);

        $this->id = $this->get($this->id);
        $this->name = $this->get($this->name);
        $this->imageUrl = $this->get($this->imageUrl);
        $this->description = $this->get($this->description);

        $this->createDisplay();



    }

    public function getId()
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->id ;

    }

    public function setSolver(AssetSolver $assetSolver)
    {

        $additionalSolverParameters = $assetSolver->getAdditionalParam() ;

        $this->setBrotherEntity(AssetCollectionFactory::METADATASOLVER_VERB,$assetSolver,$additionalSolverParameters);

    }

    public function createDisplay()
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        $this->displayArray['id'] = $this->id ;
        $this->displayArray['name'] =  $this->get('name');
        $this->displayArray['description'] = $this->description ;
        $this->displayArray['imageUrl'] = $this->imageUrl ;
    }


    public function getDefaultDisplay($simple = false)
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->displayArray ;

    }

    public function getOrbFromSpecifier(BlockchainContractStandard $specifier)
    {

        $orbFactory = new OrbFactory();
        $orbs = $orbFactory->getOrbsFromContractPath($this,$specifier);

    }

    public function setImageUrl($url){

        $this->createOrUpdateRef(AssetCollectionFactory::MAIN_IMAGE,$url);
        $this->imageUrl = $url ;


    }

    public function setName($name){

        $this->createOrUpdateRef(AssetCollectionFactory::MAIN_NAME,$name);
        $this->name = $name ;


    }

    public function setDescription($description){

        $this->setStorage($description);
        $this->createOrUpdateRef(AssetCollectionFactory::DESCRIPTION,$description);
        $this->description = $description;


    }

    public function getSolvers(){

        return $this->getJoinedEntities(AssetCollectionFactory::METADATASOLVER_VERB);



    }


}