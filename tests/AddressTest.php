<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;

use CsCannon ;


final class AddressTest extends TestCase
{

    public function testXcpAddress()
    {

        $addressFacotry = CsCannon\BlockchainRouting::getAddressFactory('1mzm8NqodUuuxip3uSoDrXraCXkmmwDcq');




    }


}
