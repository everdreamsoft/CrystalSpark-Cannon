<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2019
 * Time: 16:58
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use SandraCore\EntityFactory;
use SandraCore\System;


class TokenDetailsFactory extends EntityFactory
{

    protected $isa = 'tokenPath';
    protected $file = 'tokenPathFile';
    const ID = 'code';

    public function __construct(System $sandra, $isa = "tokenPath", $file = "tokenPathFile")
    {
        parent::__construct($isa, $file, $sandra);
        $this->isa = $isa;
        $this->file = $file;
    }

    public function load(BlockchainContract $contract)
    {
        $this->setFilter("info", $contract->subjectConcept->idConcept);
        $this->populateLocal();
    }

}
