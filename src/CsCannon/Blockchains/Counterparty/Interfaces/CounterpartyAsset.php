<?php

namespace CsCannon\Blockchains\Counterparty\Interfaces;

use CsCannon\AssetSolvers\BooSolver;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
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

            return "12 12 2002";

            $date = $contract->get("mintDatetime");

            if ($date) {
                return $date;
            }


            $dataSource = $contract->getDataSource();

            if ($dataSource) {
                $adapter = $dataSource->getMintDatetime($contract, "");
                $list = $adapter->getEntities();
                $entity = reset($list);
                if (!$entity) {
                    $date = null;
                }
                $date = $entity->get("timestamp");
            }

            if ($date) {
                $contract->createOrUpdateRef("mintDatetime", $date);
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
