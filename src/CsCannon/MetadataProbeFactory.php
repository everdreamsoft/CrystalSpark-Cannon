<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 07.04.20
 * Time: 12:18
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainBlockFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Generic\GenericContractFactory;
use SandraCore\Entity;

/**
 * Class MetadataProbeFactory
 *
 * Factory for creating and query MetadataProbe
 *
 * @method MetadataProbe             createNew($dataArray, $linArray = null) : Entity()            Use the method create instead unless you know what you are doing

 */

class MetadataProbeFactory extends CSEntityFactory
{

    public static $isa = 'metadataProbe';
    public static $file = 'metadataProbeFactory'; //this has to be set in child class or will raise error
    protected static $className = MetadataProbe::class ;

    public  const URL = "url";
    public  const BIND_COLLECTION = "collection";
    public  const BIND_CONTRACT = "contract";
    public  const ON_QUEUE = "queue";
    public  const IDENTIFIER = "identifier";
    public  const TROTTLE_REQUEST = "throttleRequestToTime";

    public function create(AssetCollection $collection, BlockchainContract $contract,  $url):MetadataProbe{


        $data[self::URL] = $url ;
        $data[self::IDENTIFIER] = $this->getIdentifier($collection,$contract);



       $newProbe = $this->createNew($data);
       $newProbe->setBrotherEntity(self::BIND_COLLECTION,$collection,null);
       $newProbe->setBrotherEntity(self::BIND_CONTRACT,$contract,null);

       return $newProbe ;

    }

    private function getIdentifier(AssetCollection $collection, BlockchainContract $contract){


        return 'probe-'.$collection->getId().'-'.$contract->getId() ;
}

    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC',$sortByRef = null, $numberSort = false)
    {


        $assetCollectionFactory = new AssetCollectionFactory(SandraManager::getSandra());
        $blockchainContractFactory = new GenericContractFactory();
        $assetCollectionFactory->populateLocal();
        $populated = parent::populateLocal($limit, $offset, $asc,$sortByRef, $numberSort);
        $this->populateBrotherEntities();
        $this->joinFactory(self::BIND_COLLECTION,$assetCollectionFactory);
        $this->joinFactory(self::BIND_CONTRACT,$blockchainContractFactory);
        $this->joinPopulate();


    }

    public function getOrCreate(AssetCollection $collection, BlockchainContract $contract,  $onCreateUrl)
    {
        $probe = $this->first(self::IDENTIFIER,$this->getIdentifier($collection,$contract));

        if (!$probe) $probe = $this->create($collection,$contract,$onCreateUrl);

        return $probe ;




    }

    public function get(AssetCollection $collection, BlockchainContract $contract)
    {
        $probe = $this->first(self::IDENTIFIER,$this->getIdentifier($collection,$contract));


        return $probe ;




    }




}