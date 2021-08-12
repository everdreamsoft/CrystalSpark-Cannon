<?php

namespace CsCannon\Blockchains;

use CsCannon\AssetCollectionFactory;
use Exception;

class ChangeIssuerFactory extends BlockchainEventFactory
{

    public static $isa = 'changeIssuerEvent';
    public static $file = 'changeIssuerEventFile';

    protected static $className = ChangeIssuer::class;

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
        $this->blockchain = $blockchain;
        return parent::__construct();
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
    public function reassignCollections(): array
    {
        $this->populateLocal();
        /** @var ChangeIssuer[] $changeIssuers */
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
                $newIssuers = $change->getNewIssuer();

                if(!is_null($newIssuers)){

                    /** @var BlockchainAddress $newIssuer */
                    $newIssuer = end($newIssuers);

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