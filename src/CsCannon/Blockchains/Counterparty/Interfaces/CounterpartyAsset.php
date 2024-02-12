<?php

namespace CsCannon\Blockchains\Counterparty\Interfaces;

use CsCannon\AssetSolvers\BooSolver;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;
use CsCannon\Orb;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class CounterpartyAsset extends BlockchainContractStandard
{

    public $tokenId = null;
    public $specificatorArray = [];

    public function __construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, System $system)
    {
        $this->solver = BooSolver::class;
        parent::__construct($sandraConcept, $sandraReferencesArray, $factory, $entityId, $conceptVerb, $conceptTarget, $system);
    }

    public function setTokenId($tokenId)
    {
    }

    /**
     * @throws \Exception
     */
    public function getTokenMintDate(BlockchainContract $contract = null): ?string
    {
        if ($contract != null) {

            $date = $contract->get("mintDateTime");

            if ($date) {
                return $date;
            }

            // In case mint date is not found, get it from the DB and save it as ref on XCP contract
            XchainOnBcy::$dbHost = env('DB_HOST_XCP');
            XchainOnBcy::$db = env('DB_DATABASE_XCP');
            XchainOnBcy::$dbUser = env('DB_USERNAME_XCP');
            XchainOnBcy::$dbpass = env('DB_PASSWORD_XCP');

            $date = XchainOnBcy::getAssetBlockTime($contract->getId());

            if ($date) {
                $contract->createOrUpdateRef("mintDateTime", $date);
            }

            return $date;

        }

        return null;
    }


    //override the method to catch tokenId
    public function setTokenPath($tokenPath)
    {
        parent::setTokenPath($tokenPath);
    }

    public function getStandardName()
    {
        return "Counterparty Token";
    }

    public function getSolver()
    {

    }

    public function resolveAsset(Orb $orb)
    {
        $return = BooSolver::resolveAsset($orb, $this);
        return $return;
    }

    public function getDisplayStructure()
    {
        return null;
    }


}
