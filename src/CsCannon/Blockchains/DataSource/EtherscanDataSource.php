<?php

namespace CsCannon\Blockchains\DataSource;

use CsCannon\Balance;
use CsCannon\Blockchains\BlockchainAddress;
use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\BlockchainContractStandard;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC20;
use CsCannon\Blockchains\Ethereum\Interfaces\ERC721;
use CsCannon\Blockchains\Interfaces\UnknownStandard;
use CsCannon\BlockchainStandardFactory;
use CsCannon\SandraManager;
use SandraCore\ForeignEntityAdapter;

class EtherscanDataSource extends BlockchainDataSource
{
    public $sandra;
    public static $chainUrl;
    public static $defaultChainUrl = 'https://api.etherscan.io/api';
    protected static $apiKey;

    
    /**
     * __construct default on mainnet Etherscan, 'ropsten' or 'rinkeby' are supported as $netToUse
     * @param  String $apiKey
     * @param  String $net
     * @return void
     */
    public function __construct(string $apiKey, ?string $netToUse = null){

        if($netToUse){

            $net = strtolower($netToUse);
            
            switch ($net){

                case 'ropsten':
                    self::$chainUrl = 'https://api-ropsten.etherscan.io/api';
                break;

                case 'rinkeby':
                    self::$chainUrl = 'https://api-rinkeby.etherscan.io/api';
                break;
            }
        }

        self::setApiKey($apiKey);
    }



    public static function getEvents($contract=null,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter
    {

        $foreignAdapter = new ForeignEntityAdapter('https://ropsten.etherscan.io/api'.$address->getAddress(),'data',
            SandraManager::getSandra());

        // $foreignAdapter = new ForeignEntityAdapter('https://ropsten.etherscan.io/api'.$address->getAddress(),'data',SandraManager::getSandra());

        //$foreignEntityAdapter->addNewEtities($entityArray,array());

        return $foreignAdapter ;
    }

    public static function getBalance(BlockchainAddress $address, $limit, $offset): Balance
    {

        // TODO

        $foreignAdapter = $foreignAdapter = new ForeignEntityAdapter(self::getChainUrl()
            .'?module=account&action=balance&address='.$address->getAddress().'&apikey=21AH14S1UUEXRJRPT6PA1HVCK5DVXHTT3V','result',SandraManager::getSandra());

        $foreignAdapter->populate();

        print_r($foreignAdapter->foreignRawArray); 
        die;

        $ethContractFactory = new EthereumContractFactory();
        $ethContractFactory->populateLocal();

        $balance = new Balance($address);
        $ethUnknownStandard = UnknownStandard::init();
        $tokenStandardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
        $tokenStandardFactory->populateLocal();


        // TODO

        return $balance;

    }
    
    /**
     * getBalanceForContract return the balance of specified contract in parameter
     * @param  BlockchainAddress $address
     * @param  BlockchainContract[] $contract
     * @param  Int $limit
     * @param  Int $offset
     * @return Balance
     */
    public static function getBalanceForContract(BlockchainAddress $address, array $contract, $limit = 100, $offset = 0): Balance
    {

        $ethContractFactory = new EthereumContractFactory();
        $ethContractFactory->populateLocal();

        $balance = new Balance($address);

        foreach($contract as $currentContract){

            $urlToQuery = self::getChainUrl()
            .'?module=account&action=tokenbalance&contractaddress='.$currentContract->getId()
            .'&address='.$address->getAddress().
            '&tag=latest&apikey='.self::$apiKey;
            
            if(!$currentContract->getStandard()) {
                $standard = 
                    $currentContract->setStandard(
                        self::getAbiForStandard($currentContract, $ethContractFactory)
                    );
            }

            $standard = $currentContract->getStandard();

            $tokenStandardFactory = new BlockchainStandardFactory(SandraManager::getSandra());
            $tokenStandardFactory->populateLocal();

            $foreignAdapter = new ForeignEntityAdapter($urlToQuery,
                self::transformEntities($urlToQuery, $address, $currentContract),
                SandraManager::getSandra()
            );

            $tokens = $foreignAdapter->foreignRawArray['result'];

            $foreignAdapter->populate();

            $balance->addContractToken($currentContract, $standard, $tokens);
            
            sleep(0.25);
        }

        return $balance;


    }
    
    /**
     * transformEntities query to url for save the necessary (tokens, address and contract)
     * @param  String $urlToQuery
     * @param  BlockchainAddress $address
     * @param  BlockchainContract $contract
     * @return Array
     */
    public static function transformEntities($urlToQuery, BlockchainAddress $address, BlockchainContract $contract){

        $jason = file_get_contents($urlToQuery);

        $obj = json_decode($jason);

        $entityArray = array();

        $entityArray['quantity'] = $obj->result;
        $entityArray['contract'] = $contract->getId();
        $entityArray['address'] = $address->getAddress();

        return $entityArray;

    }
    
    /**
     * getAbiForStandard find the Ethereum standard for the contract (ERC20 or ERC721)
     * @param  BlockchainContract $contract
     * @param  BlockchainContractFactory $contractFactory
     * @return BlockchainContractStandard $standard
     */
    public static function getAbiForStandard(BlockchainContract $contract, BlockchainContractFactory $contractFactory): BlockchainContractStandard
    {

        $response = file_get_contents(self::$chainUrl
            .'?module=contract&action=getabi&address='.$contract->getId()
            .'&apikey='.self::$apiKey);

        $obj = json_decode($response);
        $result = json_decode($obj->result);

        // $standard = $contractFactory->get($contract->getId(), true, ERC20::init());
        $standard = ERC20::init();

        foreach($result as $value) {

            if(isset($value->name)){

                if ($value->name == "uniqueTokens") {

                    $standard = ERC721::init();
                    // $standard = $contractFactory->get($contract->getId(), true, ERC721::init());
                break;
                }
            }
        }

        return $standard;

    }


    public static function getChainUrl(): String
    {

       if (!isset(self::$chainUrl)){

           self::$chainUrl = self::$defaultChainUrl ;

       }

       return self::$chainUrl;


    }

    public static function getApiKey(){
        return self::$apiKey;
    }

    public static function setApiKey($apiKey){
        self::$apiKey = $apiKey;
    }
}
