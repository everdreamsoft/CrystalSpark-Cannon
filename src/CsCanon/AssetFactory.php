<?php

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.04.19
 * Time: 14:36
 */

namespace CsCanon;



use App\Blockchains\BlockchainTokenFactory;
use SandraCore\System;

class AssetFactory extends \SandraCore\EntityFactory
{


    protected static $className = 'App\Asset' ;
    protected static $isa = 'blockchainizableAsset';
    protected static $file = 'blockchainizableAssets';
    public static $tokenJoinVerb = 'bindToToken';
    public static $collectionJoinVerb = 'bindToCollection';



    public function __construct(System $system = null)
    {



        if (is_null($system)) $system = app('Sandra')->getSandra();

        parent::__construct(self::$isa, self::$file, $system);

        $this->generatedEntityClass = self::$className ;
    }

    public function joinToken (BlockchainTokenFactory $factory){

        $this->joinFactory(AssetFactory::$tokenJoinVerb,$factory);


    }

    public function joinCollection (AssetCollectionFactory $factory){

        $this->joinFactory(AssetFactory::$collectionJoinVerb,$factory);


    }




}