<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-25
 * Time: 09:33
 */

namespace CsCannon\Tests;


use CsCannon\SandraManager;
use SandraCore\EntityFactory;
use SandraCore\System;

class TestManager
{

    public  const XCP_TEST_ADDRESS = '13nKtyg7i77EFaU2wYvyepibopRmd3CBX1';
    public  const XCP_TOKEN_AVAIL = 'BFORDRKMCXVI'; //this token should be on previous address
    public  const XCP_TOKEN_QUANTITY = 2; //The quantity should be this

    public const LIMIT_TO_COLLECTIONS = ['eDie'];


    public  const ETHEREUM_TEST_ADDRESS = '0xcB4472348cBd828dEAa5bc360aEcdcFC87332C79';
    public  const ETHEREUM_TOKEN_AVAIL = '0xd73be539d6b2076bab83ca6ba62dfe189abc6bbe'; //contract address
    public  const ETHEREUM_TOKEN_ID = '47225'; //contract address


    public static function initTestDatagraph(){



        // TestManagerPrivate::initTestDatagraph() ;
        //return

        $sandraToFlush = new System('phpUnit_', true);
        \SandraCore\Setup::flushDatagraph($sandraToFlush);
        $sandraToFlush->destroy();
        $system = new System('phpUnit_',true);
        $system->registerStructure = true ;


        SandraManager::setSandra($system);


    }

    public static function registerDataStructure(){


        $sandraTest = SandraManager::getSandra();
        $factoryArray = $sandraTest->registerFactory ;

        if(!is_array($factoryArray)) return ;

        $defaultSandra  = SandraManager::getDefaultSandra();
        SandraManager::setSandra($defaultSandra);

    foreach ($factoryArray as $factory){

        /** @var EntityFactory $factory */

        $factoryClass = get_class($factory);
        $factoryToCreateView = new $factoryClass($defaultSandra);

        $name = (new \ReflectionClass($factoryToCreateView))->getShortName();
        /** @var EntityFactory $factoryToCreateView */
        $factoryToCreateView->populateLocal();
        $factoryToCreateView->createViewTable($name);


    }

    //Swith back to the default sandra
        SandraManager::setSandra($sandraTest);


    }


}