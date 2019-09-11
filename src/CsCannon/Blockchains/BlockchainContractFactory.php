<?php

namespace CsCannon\Blockchains;

use CsCannon\AssetCollectionFactory;
use CsCannon\Blockchains\BlockchainAddressFactory;

use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

class BlockchainContractFactory extends EntityFactory
{

const TOKENID = 'tokenId';
protected static $file = 'blockchainContractFile';
protected static $isa = null;
protected static $className = 'CsCannon\BlockchainContract';
public const  MAIN_IDENTIFIER = 'id';
public  const JOIN_COLLECTION = 'inCollection';

    public function __construct(){

        parent::__construct(static::$isa,static::$file,SandraManager::getSandra());



        $this->generatedEntityClass = static::$className ;




    }


    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC')
    {

        $return = parent::populateLocal($limit, $offset, $asc);

        $collectionFactory = AssetCollectionFactory::getStaticCollection();

        $this->joinFactory(static::JOIN_COLLECTION,$collectionFactory);
        $this->joinPopulate();


    }

    public function get($identifier,$autoCreate=false):?BlockchainContract
    {

        $return = $this->first(self::MAIN_IDENTIFIER,$identifier);
        /** @var BlockchainContract $return */

        $identifierName = self::MAIN_IDENTIFIER;
        $entity = $this->first($identifierName,$identifier);

        $foreignAdapter = new ForeignEntityAdapter('http://www.google.com','',SandraManager::getSandra());


        if(is_null($entity) && !$autoCreate){
            $refConceptId = CommonFunctions::somethingToConceptId(static::$isa,SandraManager::getSandra());
            $entity = new static::$className("foreignContract:$identifier",array($identifierName => $identifier),$foreignAdapter,$this->entityReferenceContainer, $this->entityContainedIn, "foreign$identifier",$this->system);
            $this->addNewEtities($entity,array($refConceptId=>$entity));

            //dd($entity);

        }

        return $entity ;




    }



// legacy
    public function resolveMetaData (){


    return 'helloMeta';

}







}
