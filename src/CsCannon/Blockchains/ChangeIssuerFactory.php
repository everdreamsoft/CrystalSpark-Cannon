<?php

namespace CsCannon\Blockchains;

use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\SandraManager;
use Exception;
use SandraCore\Entity;
use SandraCore\EntityFactory;


class ChangeIssuerFactory extends EntityFactory
{

    public static string $isa = 'changeIssuer';
    public static string $file = 'changeIssuerFile';

    protected static string $className = 'CsCannon\Blockchains\ChangeIssuer';

    public Blockchain $blockchain;

    const EVENT_SOURCE_ADDRESS = 'source';
    const EVENT_BLOCK_TIME = 'timestamp';
    const ON_BLOCKCHAIN = 'onBlockchain';
    const EVENT_BLOCK = 'onBlock';

    const NEW_ISSUER = 'newIssuer';
    const COLLECTION_ID = 'collectionId';
    const IS_REASSIGNED = "reassigned";
    const REASSIGNED = 'true';

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct(static::$isa, static::$file, SandraManager::getSandra());
        $this->generatedEntityClass = static::$className;
        $this->blockchain = $blockchain;
    }


    public function populateLocal($limit = 1000, $offset = 0, $asc = 'DESC', $sortByRef = null, $numberSort = false)
    {
        $populated = parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        $blockchain = $this->blockchain;

        $this->joinFactory(ChangeIssuerFactory::EVENT_SOURCE_ADDRESS, $blockchain->getAddressFactory());
        $this->joinFactory(ChangeIssuerFactory::NEW_ISSUER, $blockchain->getAddressFactory());
        $this->joinPopulate();

        return $populated;
    }

    /**
     * @return ChangeIssuer[]
     */
    public function getEntities():array
    {
        return parent::getEntities();
    }


    /**
     * @param Blockchain $blockchain
     * @param BlockchainAddress $source
     * @param BlockchainAddress $newIssuer
     * @param BlockchainBlock $block
     * @param string $collectionId
     * @param string $txId
     * @param string $timestamp
     * @return Entity
     */
    public function createChangeIssuer(
        Blockchain $blockchain,
        BlockchainAddress $source,
        BlockchainAddress $newIssuer,
        BlockchainBlock $block,
        string $collectionId,
        string $txId,
        string $timestamp
    ): Entity
    {

        $dataArray[self::COLLECTION_ID] = $collectionId;
        $dataArray[Blockchain::$txidConceptName] = $txId;
        $dataArray[self::EVENT_BLOCK_TIME] = $timestamp;

        $triplets[self::ON_BLOCKCHAIN] = $blockchain::NAME;

        $triplets[self::EVENT_BLOCK] = $block;
        $triplets[self::EVENT_SOURCE_ADDRESS] = $source;
        $triplets[self::NEW_ISSUER] = $newIssuer;

        return parent::createNew($dataArray, $triplets);
    }



    /**
     * @return ChangeIssuer[]
     */
    public function reassignCollections(): array
    {
        $this->populateLocal();
        $changeIssuers = $this->getEntities();

        $collectionFactory = new AssetCollectionFactory($this->system);

        foreach ($changeIssuers as $change){

            $reassigned = $change->isAlreadyReassigned();

            if($reassigned){
                continue;
            }

            $collectionId = $change->getCollectionId();

            if(!is_null($collectionId)){

                $collToReassign = $collectionFactory->get($collectionId);
                $newIssuer = $change->getNewIssuer();

                if(!is_null($newIssuer)){

                    try{
                        $collToReassign->setOwner($newIssuer);
                        $change->setReassigned();
                    }catch(Exception $e){

                    }

                }
            }
        }
        return $changeIssuers;
    }

}