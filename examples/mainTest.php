<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 24.06.20
 * Time: 10:29
 */

use CsCannon\Blockchains\Ethereum\EthereumAddressFactory;

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

$addressToQuery = '1PDJv8u8zw4Fgqr4uCb2yim9fgTs5zfM4s';
//$addressToQuery = '0x7f7EED1fcBb2C2cf64d055eED1Ee051DD649C8e7';

$addressFactory = \CsCannon\BlockchainRouting::getAddressFactory($addressToQuery);

$address = $addressFactory->get($addressToQuery);



if ($addressFactory instanceof \CsCannon\Blockchains\Counterparty\XcpAddressFactory){
    $address->setDataSource(new \CsCannon\Blockchains\DataSource\CrystalSuiteDataSource());
    //TODO fix datasource default for xcp not working
}

$balance = $address->getBalance();

print_r($balance->getTokenBalance());

echo $address->getAddress();




