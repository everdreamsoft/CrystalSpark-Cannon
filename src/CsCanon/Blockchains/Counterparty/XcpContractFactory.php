<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;





use CsCanon\Blockchains\BlockchainAddressFactory;
use CsCanon\Blockchains\BlockchainContractFactory;

class XcpContractFactory extends XcpTokenFactory
{

    public static $isa = 'xcpToken';
    public static $file = 'xcpTokenFile';
    protected static $className = 'CsCanon\Blockchains\Counterparty\XcpContract' ;


    // On counterparty a contract is the token itself
    public function get($tokenName,$autoCreate = false){


        $entityToken = $this->getOrCreateFromRef('tokenId', $tokenName);








        return $entityToken ;

    }

    public function resolveMetaData (){




    }









}