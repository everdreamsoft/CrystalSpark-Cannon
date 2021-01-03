<?php

namespace CsCannon\Blockchains\Ethereum\DataSource;


use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Balance;
use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
use CsCannon\SandraManager;
use Ethereum\DataType\Block;
use Ethereum\DataType\EthB;
use Ethereum\DataType\EthBlockParam;
use Ethereum\DataType\EthQ;
use Ethereum\DataType\FilterChange;
use Ethereum\Ethereum;
use Ethereum\SmartContract;
use Illuminate\Support\Facades\DB;
use SandraCore\ForeignEntity;
use SandraCore\ForeignEntityAdapter;
use SandraCore\PdoConnexionWrapper;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:53
 */
class OpenSeaRinkebyDatasource extends OpenSeaImporter
{


    public static $apiUrl = 'https://rinkeby-api.opensea.io/api/v1/';





}