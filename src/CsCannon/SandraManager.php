<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-07-08
 * Time: 13:51
 */

namespace CsCannon;


use SandraCore\System;

class SandraManager
{

    private static $instanceSandra ;

    public static function getSandra(){

        if (is_null(self::$instanceSandra)){

            self::$instanceSandra = new System('',true);


        }

        return self::$instanceSandra ;

    }

    public static function setSandra(System $sandra){

        self::$instanceSandra = $sandra ;

        return self::$instanceSandra ;

    }

}