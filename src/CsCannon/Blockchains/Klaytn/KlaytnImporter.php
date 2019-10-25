<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:50
 */

namespace CsCannon\Blockchains\Ethereum;


use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Counterparty\DataSource\DatagraphSource;
use CsCannon\Blockchains\Counterparty\DataSource\XchainOnBcy;
use CsCannon\Blockchains\Ethereum\DataSource\OpenSeaImporter;
use CsCannon\Blockchains\Ethereum\DataSource\phpWeb3;
use CsCannon\Blockchains\Klaytn\KlaytnBlockchain;

class KlaytnImporter extends BlockchainImporter
{

    public $defaultDataSource = DatagraphSource::class ;
    public $blockchain = KlaytnBlockchain::class ;








}