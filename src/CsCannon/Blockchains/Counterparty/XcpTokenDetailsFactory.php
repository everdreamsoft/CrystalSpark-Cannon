<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 05.04.19
 * Time: 16:29
 */

namespace CsCannon\Blockchains\Counterparty;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\Counterparty\Interfaces\CounterpartyAsset;
use CsCannon\TokenDetailsFactory;
use SandraCore\System;

class XcpTokenDetailsFactory extends TokenDetailsFactory
{

    public function __construct(System $sandra, BlockchainContract $contract)
    {
        parent::__construct($sandra, $contract, "xcpContract", "xcpContractFile");
    }

    public function loadDetails()
    {
        new CounterpartyAsset(
            $this->contract->subjectConcept,
            $this->contract->entityRefs,
            $this, $this->contract->entityId,
            $this->contract->verbConcept,
            $this->contract->targetConcept,
            $this->system);
    }

}
