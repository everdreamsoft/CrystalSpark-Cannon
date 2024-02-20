<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContractStandard;
use SandraCore\EntityFactory;
use SandraCore\System;

/**
 * Class TokenPathToAssetFactory
 *
 * Factory to get create join token path to specific assets. For example tokenId = 1 =>
 *
 * @method TokenPathToAssetFactory             createNew($dataArray, $linArray = null, $autocommit = true) : Entity()            Use the method create instead unless you know what you are doing
 */
class TokenPathToAssetFactory extends EntityFactory
{

    protected static $isa = 'tokenPath';
    protected static $file = 'tokenPathFile';

    const ID = 'code';

    const JOINED_INFO = "info";
    const MINT_DATETIME_SHORTNAME = "mintDatetime";

    public function __construct(System $sandra)
    {

        parent::__construct(static::$isa, static::$file, $sandra);


    }

    public function get($identifierString)
    {


        return $this->last(self::ID, $identifierString);

    }


    public function getOrCreate(BlockchainContractStandard $standard)
    {

        $result = $this->get($standard->getDisplayStructure());
        if ($result == null) $result = $this->create($standard);

        return $result;

    }


    public function create(BlockchainContractStandard $specifier, $autocommit = true)
    {

        $sandra = SandraManager::getSandra();

        $specifier->verifyTokenPath($specifier->getSpecifierData());

        //check if doens't exist in db
        $displayStructure = $specifier->getDisplayStructure();

        $conceptForSearch = new TokenPathToAssetFactory($sandra);
        $result = $conceptForSearch->getOrCreateFromRef($this::ID, $displayStructure);
        $this->addNewEtities([$result->subjectConcept->idConcept => $result], $conceptForSearch->sandraReferenceMap);

        return $result;


    }


}
