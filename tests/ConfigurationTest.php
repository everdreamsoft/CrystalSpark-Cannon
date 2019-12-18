<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;


final class ConfigurationTest extends TestCase
{

    public function testConfiguration()
    {


        \CsCannon\Tests\TestManager::initTestDatagraph();

        $instanceManager = new \CsCannon\InstanceManager();
        $instanceManager->getConfiguration(\CsCannon\SandraManager::getSandra());






    }









}
