<?php

namespace CsCannon\Blockchains;


use CsCannon\Blockchains\Generic\GenericBlockchain;
use CsCannon\SandraManager;
use SandraCore\EntityFactory;

class BlockchainBlockFactory extends EntityFactory
{
    public $blockchain;
    public $isa;
    public $file;
    public $foreignAdapterX;
    protected static $className = 'CsCannon\Blockchains\BlockchainBlock';
    const  INDEX_SHORTNAME = 'blockIndex';
    const  BLOCK_TIMESTAMP = 'timestamp';


    public function __construct(Blockchain $blockchain)
    {

        $blockIsa = null;
        if (!($blockchain instanceof GenericBlockchain)) {
            $blockIsa = $blockchain::$blockchainConceptName . 'Bloc';
        }


        $this->file = $blockchain::$blockchainConceptName . 'BlocFile';
        $this->entityIsa = $blockIsa;


        parent::__construct($blockIsa, $this->file, SandraManager::getSandra());

        $this->generatedEntityClass = static::$className;

    }

    public function get($id): ?BlockchainBlock
    {
        return $this->first(self::INDEX_SHORTNAME, $id);
    }

    public static function getOrCreateBlockWithId($id, Blockchain $blockchain): BlockchainBlock
    {
        $blockFactory = new BlockchainBlockFactory($blockchain);
        return $blockFactory->getOrCreateFromRef(self::INDEX_SHORTNAME, $id);
    }

    public static function getTimestampPrefix($chain): string
    {
        return (strlen($chain) > 0) ? $chain . "-" : "";
    }


}
