<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCannon;

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

    $this->displayArray['id'] = $this->id ;
    $this->displayArray['name'] =  $this->get('name');
    $this->displayArray['description'] = $this->description ;
    $this->displayArray['imageUrl'] = $this->imageUrl ;

}

    public function getId()
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->id ;

    }

    public function getDefaultDisplay($simple = false)
    {

        // $this->id = $sandraReferencesArray[$system->systemConcept->get($this->id)];

        return $this->displayArray ;

    }


}