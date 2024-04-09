<?php
/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 2024-04-09
 * Time: 10:00
 */

namespace CsCannon\AssetSolvers;


use CsCannon\AssetCollection;
use CsCannon\AssetFactory;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\SandraManager;

class UnassignedSolver extends LocalSolver
{

    public static function getSolverIdentifier(): string
    {
        return "unassignedSolver";
    }

    public static function resolveAsset(AssetCollection $assetCollection, BlockchainContractStandard $specifier, BlockchainContract $contract): ?array
    {
        if (self::getLastUpdate() == null) self::update();

        $assets = parent::resolveAsset($assetCollection, $specifier, $contract);
        if ($assets) return $assets;

        $assetFact = new AssetFactory(SandraManager::getSandra());

        return $assetFact->populateFromSearchResults("nullAsset");


    }


}
