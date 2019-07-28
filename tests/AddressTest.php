<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.07.2019
 * Time: 17:46
 */


require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use PHPUnit\Framework\TestCase;




final class AddressTest extends TestCase
{

    public function testXcpAddress()
    {

        $testAddress = '1mzm8NqodUuuxip3uSoDrXraCXkmmwDcq' ;

        $addressFacotry = CsCannon\BlockchainRouting::getAddressFactory('1mzm8NqodUuuxip3uSoDrXraCXkmmwDcq');
        $addressEntity = $addressFacotry->get($testAddress);

        /** @var \CsCannon\Blockchains\Counterparty\XcpAddress $addressEntity */

        $addressEntity->getBalance();

        print_r( $addressEntity->getBalance());




    }


}
