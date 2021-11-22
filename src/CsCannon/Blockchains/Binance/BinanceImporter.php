<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 15.11.2021
 * Time: 09:50
 */

namespace CsCannon\Blockchains\Binance;


use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;
use CsCannon\Blockchains\Binance\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Binance\DataSource\phpWeb3;

class BinanceImporter extends BlockchainImporter
{
    public $defaultDataSource = OpenSeaImporter::class ;
    public $blockchain = BinanceBlockchain::class ;
}