<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-07-08
 * Time: 13:51
 */

namespace CsCannon;


use phpDocumentor\Reflection\Types\Object_;
use SandraCore\EntityFactory;
use SandraCore\System;

class SandraManager
{

    private static $instanceSandra ;

    public static function getDefaultSandra(){



            $sandra = new System('romeo',true);
        return $sandra ;

    }

    public static function getSandra(){

        if (is_null(self::$instanceSandra)){


            self::$instanceSandra = self::getDefaultSandra();


        }

        return self::$instanceSandra ;

    }

    public static function setSandra(System $sandra){

        self::$instanceSandra = $sandra ;

        return self::$instanceSandra ;

    }

    public static function dispatchError(System $sandra,$code,$level,$message,Object $sender){

        self::$instanceSandra = $sandra ;

        $callerClass = get_class ( $sender  ) ;

        $sandra->systemError($code,$callerClass,$level,$message);


        //Code 1 collection exists
        //Code 2 Asset exists


    }

    public static function registerDataStructure(System $sandra){


       ;
        $factoryArray = $sandra->registerFactory ;

        if(!is_array($factoryArray)) return ;



        foreach ($factoryArray as $factory){

            /** @var EntityFactory $factory */

            $factoryClass = get_class($factory);
            $factoryToCreateView = new $factoryClass();

            $name = (new \ReflectionClass($factoryToCreateView))->getShortName();
            /** @var EntityFactory $factoryToCreateView */
            $factoryToCreateView->populateLocal();
            $factoryToCreateView->createViewTable($name);


        }



    }

}