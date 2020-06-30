<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 29.06.20
 * Time: 15:42
 */

namespace CsCannon;


use SandraCore\System;

class MySystem extends System
{

    public function __construct($env = '', $install = false, $dbHost = '127.0.0.1', $db = 'sandra', $dbUsername = 'root', $dbpassword = '54245')
    {
        parent::__construct($env, $install, $dbHost, $db, $dbUsername, $dbpassword);
    }

}