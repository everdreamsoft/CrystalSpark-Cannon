<?php

namespace CsCannon\Blockchains;

use CsCannon\AssetCollectionFactory;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainAddressFactory;

use CsCannon\Blockchains\Interfaces\UnknownStandard;
use CsCannon\BlockchainStandardFactory;
use CsCannon\SandraManager;
use SandraCore\CommonFunctions;
use SandraCore\Entity;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;

class BlockchainContractFactory extends EntityFactory
{

const TOKENID = 'tokenId';
public static $file = 'blockchainContractFile';
protected static $isa = null;
protected static $className = 'CsCannon\Blockchains\BlockchainContract';
public const  MAIN_IDENTIFIER = 'id';
public  const JOIN_COLLECTION = 'inCollection';
public  const JOIN_ASSET = 'joinAsset';
public  const CONTRACT_STANDARD = 'contractStandard';

    public function __construct(){

        parent::__construct(static::$isa,static::$file,SandraManager::getSandra());

        $this->generatedEntityClass = static::$className ;


    }


    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC')
    {

        $return = parent::populateLocal($limit, $offset, $asc);

        $standardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
        $collectionFactory = AssetCollectionFactory::getStaticCollection();

        $this->populateBrotherEntities(self::CONTRACT_STANDARD);


        $this->joinFactory(static::JOIN_COLLECTION,$collectionFactory);
        $this->joinFactory(self::CONTRACT_STANDARD,$standardFactory);
        $this->joinPopulate();

        return $return ;


    }

    public function get($identifier,$autoCreate=false,BlockchainContractStandard $contractStandard = null):?BlockchainContract
    {

        $return = $this->first(self::MAIN_IDENTIFIER,$identifier);
        /** @var BlockchainContract $return */



        $identifierName = self::MAIN_IDENTIFIER;
        $entity = $this->first($identifierName,$identifier);



        if ($contractStandard == null){

            $contractStandard = UnknownStandard::init();
        }

        $contractStandardEnt = $contractStandard::getEntity();




        $foreignAdapter = new ForeignEntityAdapter(null,'',SandraManager::getSandra());


        if(is_null($entity) && !$autoCreate){
            $refConceptId = CommonFunctions::somethingToConceptId(static::$isa,SandraManager::getSandra());
            $entity = new static::$className("foreignContract:$identifier",array($identifierName => $identifier),$foreignAdapter,$this->entityReferenceContainer, $this->entityContainedIn, "foreign$identifier",$this->system);
            $this->addNewEtities($entity,array($refConceptId=>$entity));

            //dd($entity);

        }

        if(is_null($entity) && $autoCreate){

            if(empty($identifier)){
                die("empty identifier");

            }

            $timstamp['creationTimestamp'] = time();
            $array = [self::CONTRACT_STANDARD => [$contractStandardEnt->subjectConcept->idConcept=>$timstamp]];

            $entity = $this->createNew(array(self::MAIN_IDENTIFIER=>$identifier),
                $array
            );



            //dd($entity);

        }

        return $entity ;


    }



// legacy
    public function resolveMetaData (){


    return 'helloMeta';

}







}
