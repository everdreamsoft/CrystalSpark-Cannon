<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-25
 * Time: 09:33
 */

namespace CsCannon\Tests;


use CsCannon\SandraManager;
use SandraCore\System;

class TestManager
{

    public  const XCP_TEST_ADDRESS = '13nKtyg7i77EFaU2wYvyepibopRmd3CBX1';
    public  const XCP_TOKEN_AVAIL = 'BFORDRKMCXVI'; //this token should be on previous address
    public  const XCP_TOKEN_QUANTITY = 2; //The quantity should be this


    public  const ETHEREUM_TEST_ADDRESS = '0x7f7EED1fcBb2C2cf64d055eED1Ee051DD649C8e7';
    public  const ETHEREUM_TOKEN_AVAIL = '0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe'; //contract address
    public  const ETHEREUM_TOKEN_ID = '47225'; //contract address


    public static function initTestDatagraph(){

        $sandraToFlush = new System('phpUnit_', true);
        \SandraCore\Setup::flushDatagraph($sandraToFlush);
        $system = new System('phpUnit_',true);


        SandraManager::setSandra($system);


    }


}