<?php

namespace CsCannon\Blockchains;

use CsCannon\Blockchains\Blockchain;
use CsCannon\BlockchainStandardFactory;
use CsCannon\Displayable;
use CsCannon\SandraManager;
use CsCannon\TokenPathToAssetFactory;
use SandraCore\EntityFactory;
use SandraCore\System;

class BlockchainEmoteFactory extends EntityFactory
{

    public static $isa = 'emoteEvent';
    public static $file = 'emoteEventFile';

    protected static $className = BlockchainEmote::class;

    private $blockchain;
    private $referenceToFound;

    const SN = "code";
    const EMOTE_ID = "emoteId";
    const EMOTE_SOURCE_ADDRESS = 'source';
    const EVENT_BLOCK_TIME = 'timestamp';
    const ON_BLOCKCHAIN = 'onBlockchain';
    const EMOTE_BLOCK = 'onBlock';
    const EMOTE_UNICODE = 'emote';
    const TARGET_CONTRACT = "targetContract";
    const TARGET_TOKEN = "targetToken";
    const BLOCKCHAIN_EVENT_TYPE_VERB = "blockchainEventType";

    const IS_ACTIVE = "isActive";

    public function __construct(Blockchain $blockchain)
    {
        parent::__construct(static::$isa, static::$file, SandraManager::getSandra());

        $this->blockchain = $blockchain;
    }


    /**
     * @param BlockchainAddress $source
     * @param BlockchainContract $contract
     * @param string $txHash
     * @param int $timestamp
     * @param BlockchainBlock $block
     * @param BlockchainContractStandard $token
     * @param string $unicode
     * @return BlockchainEmote
     */
    public function createEmote(
        BlockchainAddress $source,
        BlockchainContract $contract,
        string $txHash,
        int $timestamp,
        BlockchainBlock $block,
        BlockchainContractStandard $token,
        string $unicode
    ):BlockchainEmote{

        $contractId = null;
        $tokenId = null;

        $contractRefId = $contract->getReference('id');
        if(!is_null($contractRefId)){
            $contractId = $contractRefId->refValue;
        }

        $tokenRef = $token->getReference('code');
        if(!is_null($tokenRef)){
            $tokenId = $tokenRef->refValue;
        }

        if($tokenId && $contractId){
            $emoteId = $source->getAddress()."-".$unicode."-".$contractId."-".$tokenId;
            $dataArray[self::EMOTE_ID] = $emoteId;
        }

        $dataArray[self::EMOTE_ID] = $unicode;
        $dataArray[self::EVENT_BLOCK_TIME] = strval($timestamp);
//        $dataArray['txHash'] ??

        $triplets[self::EMOTE_BLOCK] = $block;
        $triplets[self::ON_BLOCKCHAIN] = $this->blockchain::NAME;
        $triplets[self::EMOTE_SOURCE_ADDRESS] = $source;
        $triplets[self::TARGET_CONTRACT] = $contract;

        $structure = $token->getSpecifierData();
        $triplets[self::TARGET_TOKEN] = array($token->subjectConcept->idConcept => $structure['sn']);

        /** @var BlockchainEmote $emote */
        $emote = parent::createNew($dataArray, $triplets);

        return $emote;
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $asc
     * @param null $sortByRef
     * @param false $numberSort
     * @return array
     */
    public function populateLocal($limit = 10000, $offset = 0, $asc = 'ASC', $sortByRef = null, $numberSort = false): array
    {
        $populated =  parent::populateLocal($limit, $offset, $asc, $sortByRef, $numberSort);
        $blockchain = $this->blockchain ;

        parent::populateLocal();
        $this->joinFactory(self::EMOTE_SOURCE_ADDRESS, $blockchain->getAddressFactory());
        $this->joinFactory(self::TARGET_CONTRACT, $blockchain->getContractFactory());
        $this->joinFactory(self::TARGET_TOKEN, new BlockchainStandardFactory($this->system));
        $this->joinPopulate();

        return $populated;
    }

    /**
     * $needle param can be address, token ID or block number
     *
     * @param string $needle
     * @return BlockchainEmote[]
     */
    public function getEmotes(string $needle): array
    {
        $entity = $this->searchJoinedEntity($needle);
        if(!is_null($entity)){
            foreach ($entity as $verb => $joinedEntity){
                $this->setFilter($verb, $joinedEntity);
            }
        }

        $this->populateLocal();
        /** @var BlockchainEmote[] $emotes */
        $emotes = $this->getEntities();

        return $emotes;
    }


    /**
     * @param string $needleEntity
     * @return array|null
     */
    private function searchJoinedEntity(string $needleEntity): ?array
    {
        $blockchain = $this->blockchain;

        $addressFactory = $blockchain->getAddressFactory();
        $contractFactory = $blockchain->getContractFactory();
        $blockFactory = $blockchain->getBlockFactory();

        $response = [];

        $address = $addressFactory->first($addressFactory::ADDRESS_SHORTNAME, $needleEntity);
        if(!is_null($address)){
            $response[self::EMOTE_SOURCE_ADDRESS] = $address;
            return $response;
        }

        $contract = $contractFactory->first($contractFactory::MAIN_IDENTIFIER, $needleEntity);
        if(!is_null($contract)){
            $response[self::TARGET_CONTRACT] = $contract;
            return $response;
        }

        $block = $blockFactory->first($blockFactory::INDEX_SHORTNAME, $needleEntity);
        if(!is_null($block)){
            $response[self::EMOTE_BLOCK] = $block;
            return $response;
        }

        return null;
    }


    /**
     * @param BlockchainEmote[] $emotes
     * @return BlockchainEmote[]
     */
    private function activeEmotes(array $emotes): array
    {
        $isActive = count($emotes) == 1 || count($emotes) % 2 != 0;

        foreach ($emotes as $emote){
            $emote->createOrUpdateRef(self::IS_ACTIVE, $isActive);
        }

        return $emotes;
    }


    /**
     * @param BlockchainEmote[] $emotes
     * @return array
     */
    public function getViewResponse(array $emotes): array
    {
        $uniquesEmotes = [];
        $emotesWithSameId = [];

        foreach ($emotes as $emote){
            $id = $emote->getReference(self::EMOTE_ID)->refValue ?? null;

            if(!is_null($id) && !in_array($id, $emotesWithSameId)){
                $this->referenceToFound = $id;
                $emotesWithSameId[] = $id;

                $emotesWithId = array_filter($emotes, [$this, 'filterEmotesById']);
                $this->activeEmotes($emotesWithId);

                if(!in_array(false, $emotesWithId)){
                    $finalEmote = end($emotesWithId);
                    $uniquesEmotes[] = $finalEmote;
                }
            }
        }


        $filterByContract = $this->getEmotesByVerbAndRef($uniquesEmotes, 'id', 'filterEmotesByContract', BlockchainEmoteFactory::TARGET_CONTRACT);

        $response = [];
        foreach ($filterByContract as $contractId => $emotesByContract){
            /** @var BlockchainEmote[] $emotesByContract */
            $filterBySn = $this->getEmotesByVerbAndRef($emotesByContract, BlockchainEmoteFactory::SN, 'filterEmotesBySn', BlockchainEmoteFactory::TARGET_TOKEN);

            $emotesSn = [];
            foreach ($filterBySn as $sn => $emotesBySn){
                /** @var BlockchainEmote[] $emotesBySn */
                $filterByUnicode = $this->getEmotesByVerbAndRef($emotesBySn, BlockchainEmoteFactory::EMOTE_UNICODE, 'filterByUnicode');

                $emotesUnicode = [];
                foreach ($filterByUnicode as $unicode => $emotes){
                    /** @var BlockchainEmote[] $emotes */

                    $emotesByAddresses = [];
                    foreach ($emotes as $emote){
                        $sources = $emote->getJoinedEntities(BlockchainEmoteFactory::EMOTE_SOURCE_ADDRESS);
                        /** @var BlockchainAddress $source */
                        $source = end($sources);
                        $emotesByAddresses[$source->getAddress()] = $emote->getReference(BlockchainEmoteFactory::IS_ACTIVE)->refValue ?? null;
                    }
                    $emotesUnicode[$unicode] = $emotesByAddresses;
                }
                $emotesSn[$sn] = $emotesUnicode;
            }
            $response[$contractId] = $emotesSn;
        }
        return $response;
    }


    /**
     * @param BlockchainEmote[] $emotes
     * @param string $ref
     * @param string $callbackFilter
     * @param string $verb
     * @return array
     */
    private function getEmotesByVerbAndRef(array $emotes, string $ref, string $callbackFilter, string $verb = ''): array
    {
        $response = [];
        $referencesFound = [];
        foreach ($emotes as $emote){

            if($verb != ''){
                $entitiesNeeded = $emote->getJoinedEntities($verb);
                $entityNeeded = end($entitiesNeeded);
            }else{
                $entityNeeded = $emote;
            }

            /** @var string $reference */
            $reference = $entityNeeded->getReference($ref)->refValue ?? null;

            if(!is_null($reference) && !in_array($ref, $referencesFound)){
                $this->referenceToFound = $reference;
                $referencesFound[] = $reference;

                $filterResult = array_filter($emotes, [$this, $callbackFilter]);
                if(!in_array(false, $filterResult)){
                    $response[$reference] = $filterResult;
                }
            }
        }
        return $response;
    }



    /**
     * @param BlockchainEmote $emote
     * @return bool
     */
    private function filterByUnicode(BlockchainEmote $emote): bool
    {
        $unicode = $emote->getReference(BlockchainEmoteFactory::EMOTE_UNICODE)->refValue ?? null;
        return $this->referenceToFound == $unicode;
    }

    /**
     * @param BlockchainEmote $emote
     * @return bool
     */
    private function filterEmotesBySn(BlockchainEmote $emote): bool
    {
        $tokens = $emote->getJoinedEntities(BlockchainEmoteFactory::TARGET_TOKEN);
        $token = end($tokens);
        $sn = $token->getReference(BlockchainEmoteFactory::SN)->refValue ?? null;

        return $this->referenceToFound == $sn;
    }

    /**
     * @param BlockchainEmote $emote
     * @return bool
     */
    private function filterEmotesByContract(BlockchainEmote $emote): bool
    {
        $contracts = $emote->getJoinedEntities(BlockchainEmoteFactory::TARGET_CONTRACT);
        $contract = end($contracts);
        $contractId = $contract->getReference('id')->refValue ?? null;

        return $this->referenceToFound == $contractId;
    }


    /**
     * @param BlockchainEmote $emote
     * @return bool
     */
    private function filterEmotesById(BlockchainEmote $emote): bool
    {
        return $emote->getReference(BlockchainEmoteFactory::EMOTE_ID)->refValue == $this->referenceToFound;
    }


}