<?php

namespace CsCannon\Blockchains\Ethereum\DataSource;

use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\BlockchainDataSource;
use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Blockchains\BlockchainImporter;
use CsCannon\erc721Balance;
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
class phpWeb3 extends BlockchainDataSource
{

    public $sandra ;




    public function getBalance($contract,$batchMax=1000,$offset=0):ForeignEntityAdapter{


        $abi = \GuzzleHttp\json_decode(stripslashes('[{\"constant\":true,\"inputs\":[{\"name\":\"_interfaceId\",\"type\":\"bytes4\"}],\"name\":\"supportsInterface\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"}],\"name\":\"getProto\",\"outputs\":[{\"name\":\"exists\",\"type\":\"bool\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"season\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"name\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"getApproved\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"approve\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"from\",\"type\":\"address\"},{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"transferAllFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"governor\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"migrated\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"burnAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"totalSupply\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"InterfaceId_ERC165\",\"outputs\":[{\"name\":\"\",\"type\":\"bytes4\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makePermanantlyTradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"from\",\"type\":\"address\"},{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"transferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addSpell\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"},{\"name\":\"_index\",\"type\":\"uint256\"}],\"name\":\"tokenOfOwnerByIndex\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"common\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"getActiveCards\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"unpause\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_from\",\"type\":\"address\"},{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"safeTransferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"burn\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"migrate\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"mythic\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"exists\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_index\",\"type\":\"uint256\"}],\"name\":\"tokenByIndex\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"index\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"}],\"name\":\"replaceProto\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"burnCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"name\":\"seasonTradabilityLocked\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"paused\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"},{\"name\":\"limit\",\"type\":\"uint64\"}],\"name\":\"setLimit\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"ownerOf\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"transferAll\",\"outputs\":[],\"payable\":true,\"stateMutability\":\"payable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"}],\"name\":\"balanceOf\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"name\":\"seasonTradable\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proposed\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"owns\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"approved\",\"type\":\"address\"}],\"name\":\"addPack\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"pause\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"purity\",\"type\":\"uint16\"}],\"name\":\"getShine\",\"outputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"pure\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"cards\",\"outputs\":[{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"migrateAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"getCard\",\"outputs\":[{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"}],\"name\":\"getLimit\",\"outputs\":[{\"name\":\"limit\",\"type\":\"uint64\"},{\"name\":\"set\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"symbol\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"name\":\"limits\",\"outputs\":[{\"name\":\"limit\",\"type\":\"uint64\"},{\"name\":\"exists\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_approved\",\"type\":\"bool\"}],\"name\":\"setApprovalForAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"NAME\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"rare\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proto\",\"type\":\"uint16\"}],\"name\":\"isTradable\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"transfer\",\"outputs\":[],\"payable\":true,\"stateMutability\":\"payable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proposed\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"ownsAll\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"tokenMetadataBaseURI\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"packs\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_from\",\"type\":\"address\"},{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_tokenId\",\"type\":\"uint256\"},{\"name\":\"_data\",\"type\":\"bytes\"}],\"name\":\"safeTransferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"nextSeason\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"currentSeason\",\"outputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_gov\",\"type\":\"address\"}],\"name\":\"setGovernor\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"tokenURI\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makeUntradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"random\",\"type\":\"uint16\"}],\"name\":\"getRandomCard\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"durability\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addWeapon\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addProto\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"protoCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"epic\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addMinion\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalIDs\",\"type\":\"uint16[]\"},{\"name\":\"gods\",\"type\":\"uint8[]\"},{\"name\":\"rarities\",\"type\":\"uint8[]\"},{\"name\":\"manas\",\"type\":\"uint8[]\"},{\"name\":\"attacks\",\"type\":\"uint8[]\"},{\"name\":\"healths\",\"type\":\"uint8[]\"},{\"name\":\"cardTypes\",\"type\":\"uint8[]\"},{\"name\":\"tribes\",\"type\":\"uint8[]\"},{\"name\":\"packable\",\"type\":\"bool[]\"}],\"name\":\"addProtos\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"getBurnCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"},{\"name\":\"_operator\",\"type\":\"address\"}],\"name\":\"isApprovedForAll\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"legendary\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"approveAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makeTradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"SYMBOL\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"owner\",\"type\":\"address\"},{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"name\":\"createCard\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"inputs\":[{\"name\":\"previous\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"constructor\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"id\",\"type\":\"uint256\"},{\"indexed\":false,\"name\":\"proto\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"purity\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"owner\",\"type\":\"address\"}],\"name\":\"CardCreated\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_from\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_to\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"Transfer\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_owner\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_approved\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"Approval\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_owner\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_operator\",\"type\":\"address\"},{\"indexed\":false,\"name\":\"_approved\",\"type\":\"bool\"}],\"name\":\"ApprovalForAll\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":false,\"name\":\"id\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"season\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"god\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"rarity\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"mana\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"attack\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"health\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"cardType\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"tribe\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"NewProtoCard\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[],\"name\":\"Pause\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[],\"name\":\"Unpause\",\"type\":\"event\"}]'));


        $hosts = [
            // Start testrpc, geth or parity locally.

            // This is a demo-only purpose account only.
            // Register your own access token. It's free!
            // https://infura.io/#how-to
            'https://mainnet.infura.io/v3/a6e34ed067c74f25ba705456d73a471e'
        ];

        foreach($hosts as $url) {

            try {
                $eth = new Ethereum($url);
                $contract = new SmartContract($abi, '0x6ebeaf8e8e946f0716e6533a6f2cefc83f60e8ab', $eth);
                for ($i=1;$i<300;$i++) {



                    $val = $contract->ownerOf(new EthQ($i, ['abi' => 'uint256']));
                    if($val->val()) {
                        $holders[$val->val()][] = $i ;
                    //echo "\n $i - " . $val->val();
                      //  $userBalance = erc721Balance::updateOrCreate(['id'=>'1'],['id'=>'1']);
                      // $db = erc721Balance::where('id',1);
                        $user = DB::table('erc721_balances')->updateOrInsert(['contract_unid'=>'1514','token_id'=> $i,'token_index'=> 0],['contract_unid'=>'1514','token_id'=> $i,'token_index'=> 0]) ;
                    }
                }
                print_r($holders);
                die();





            } catch (\Exception $exception) {
                echo "<p style='color: red;'>We have a problem:<br />";
                echo $exception->getMessage() . "</p>";
                echo "<pre>" . $exception->getTraceAsString() . "</pre>";
                die();
            }


        }


    }


    public function getEvents($contract,$batchMax=1000,$offset=0):ForeignEntityAdapter
    {


$abi = \GuzzleHttp\json_decode(stripslashes('[{\"constant\":true,\"inputs\":[{\"name\":\"_interfaceId\",\"type\":\"bytes4\"}],\"name\":\"supportsInterface\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"}],\"name\":\"getProto\",\"outputs\":[{\"name\":\"exists\",\"type\":\"bool\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"season\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"name\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"getApproved\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"approve\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"from\",\"type\":\"address\"},{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"transferAllFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"governor\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"migrated\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"burnAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"totalSupply\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"InterfaceId_ERC165\",\"outputs\":[{\"name\":\"\",\"type\":\"bytes4\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makePermanantlyTradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"from\",\"type\":\"address\"},{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"transferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addSpell\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"},{\"name\":\"_index\",\"type\":\"uint256\"}],\"name\":\"tokenOfOwnerByIndex\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"common\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"getActiveCards\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"unpause\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_from\",\"type\":\"address\"},{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"safeTransferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"burn\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"migrate\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"mythic\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"exists\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_index\",\"type\":\"uint256\"}],\"name\":\"tokenByIndex\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"index\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"}],\"name\":\"replaceProto\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"burnCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"name\":\"seasonTradabilityLocked\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"paused\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"},{\"name\":\"limit\",\"type\":\"uint64\"}],\"name\":\"setLimit\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"ownerOf\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"transferAll\",\"outputs\":[],\"payable\":true,\"stateMutability\":\"payable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"}],\"name\":\"balanceOf\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"name\":\"seasonTradable\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proposed\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"owns\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"approved\",\"type\":\"address\"}],\"name\":\"addPack\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"pause\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"purity\",\"type\":\"uint16\"}],\"name\":\"getShine\",\"outputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"pure\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"cards\",\"outputs\":[{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"migrateAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"getCard\",\"outputs\":[{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"id\",\"type\":\"uint16\"}],\"name\":\"getLimit\",\"outputs\":[{\"name\":\"limit\",\"type\":\"uint64\"},{\"name\":\"set\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"symbol\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"name\":\"limits\",\"outputs\":[{\"name\":\"limit\",\"type\":\"uint64\"},{\"name\":\"exists\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_approved\",\"type\":\"bool\"}],\"name\":\"setApprovalForAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"NAME\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"rare\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proto\",\"type\":\"uint16\"}],\"name\":\"isTradable\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"id\",\"type\":\"uint256\"}],\"name\":\"transfer\",\"outputs\":[],\"payable\":true,\"stateMutability\":\"payable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"proposed\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"ownsAll\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"tokenMetadataBaseURI\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"packs\",\"outputs\":[{\"name\":\"\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_from\",\"type\":\"address\"},{\"name\":\"_to\",\"type\":\"address\"},{\"name\":\"_tokenId\",\"type\":\"uint256\"},{\"name\":\"_data\",\"type\":\"bytes\"}],\"name\":\"safeTransferFrom\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[],\"name\":\"nextSeason\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"currentSeason\",\"outputs\":[{\"name\":\"\",\"type\":\"uint8\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"_gov\",\"type\":\"address\"}],\"name\":\"setGovernor\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"tokenURI\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makeUntradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"random\",\"type\":\"uint16\"}],\"name\":\"getRandomCard\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"durability\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addWeapon\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"cardType\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addProto\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"protoCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"epic\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalID\",\"type\":\"uint16\"},{\"name\":\"god\",\"type\":\"uint8\"},{\"name\":\"rarity\",\"type\":\"uint8\"},{\"name\":\"mana\",\"type\":\"uint8\"},{\"name\":\"attack\",\"type\":\"uint8\"},{\"name\":\"health\",\"type\":\"uint8\"},{\"name\":\"tribe\",\"type\":\"uint8\"},{\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"addMinion\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"externalIDs\",\"type\":\"uint16[]\"},{\"name\":\"gods\",\"type\":\"uint8[]\"},{\"name\":\"rarities\",\"type\":\"uint8[]\"},{\"name\":\"manas\",\"type\":\"uint8[]\"},{\"name\":\"attacks\",\"type\":\"uint8[]\"},{\"name\":\"healths\",\"type\":\"uint8[]\"},{\"name\":\"cardTypes\",\"type\":\"uint8[]\"},{\"name\":\"tribes\",\"type\":\"uint8[]\"},{\"name\":\"packable\",\"type\":\"bool[]\"}],\"name\":\"addProtos\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"getBurnCount\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"_owner\",\"type\":\"address\"},{\"name\":\"_operator\",\"type\":\"address\"}],\"name\":\"isApprovedForAll\",\"outputs\":[{\"name\":\"\",\"type\":\"bool\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"name\":\"legendary\",\"outputs\":[{\"name\":\"\",\"type\":\"uint16\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"to\",\"type\":\"address\"},{\"name\":\"ids\",\"type\":\"uint256[]\"}],\"name\":\"approveAll\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"season\",\"type\":\"uint8\"}],\"name\":\"makeTradable\",\"outputs\":[],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"constant\":true,\"inputs\":[],\"name\":\"SYMBOL\",\"outputs\":[{\"name\":\"\",\"type\":\"string\"}],\"payable\":false,\"stateMutability\":\"view\",\"type\":\"function\"},{\"constant\":false,\"inputs\":[{\"name\":\"owner\",\"type\":\"address\"},{\"name\":\"proto\",\"type\":\"uint16\"},{\"name\":\"purity\",\"type\":\"uint16\"}],\"name\":\"createCard\",\"outputs\":[{\"name\":\"\",\"type\":\"uint256\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"function\"},{\"inputs\":[{\"name\":\"previous\",\"type\":\"address\"}],\"payable\":false,\"stateMutability\":\"nonpayable\",\"type\":\"constructor\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"id\",\"type\":\"uint256\"},{\"indexed\":false,\"name\":\"proto\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"purity\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"owner\",\"type\":\"address\"}],\"name\":\"CardCreated\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_from\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_to\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"Transfer\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_owner\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_approved\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_tokenId\",\"type\":\"uint256\"}],\"name\":\"Approval\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":true,\"name\":\"_owner\",\"type\":\"address\"},{\"indexed\":true,\"name\":\"_operator\",\"type\":\"address\"},{\"indexed\":false,\"name\":\"_approved\",\"type\":\"bool\"}],\"name\":\"ApprovalForAll\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[{\"indexed\":false,\"name\":\"id\",\"type\":\"uint16\"},{\"indexed\":false,\"name\":\"season\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"god\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"rarity\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"mana\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"attack\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"health\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"cardType\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"tribe\",\"type\":\"uint8\"},{\"indexed\":false,\"name\":\"packable\",\"type\":\"bool\"}],\"name\":\"NewProtoCard\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[],\"name\":\"Pause\",\"type\":\"event\"},{\"anonymous\":false,\"inputs\":[],\"name\":\"Unpause\",\"type\":\"event\"}]'));


$hosts = [
            // Start testrpc, geth or parity locally.

            // This is a demo-only purpose account only.
            // Register your own access token. It's free!
            // https://infura.io/#how-to
            'https://mainnet.infura.io/v3/a6e34ed067c74f25ba705456d73a471e'
        ];




        $contract = new SmartContract($abi, '0x6ebeaf8e8e946f0716e6533a6f2cefc83f60e8ab', $eth);


        foreach($hosts as $url)
        {
            try {
                echo "<h3>What's up on $url</h3>";
                $eth = new Ethereum($url);


               $contract = new SmartContract($abi, '0x6ebeaf8e8e946f0716e6533a6f2cefc83f60e8ab', $eth);

               //take a look here
              //  https://github.com/digitaldonkey/ethereum-php-eventlistener/tree/master/app/src
               // $block_latest = $eth->eth_getBlockByNumber(new EthBlockParam('latest'), new EthB(FALSE));
                $i = 8046755 ;
                $counter = 0 ;


                $ethB = new EthB(TRUE);

                while ($counter < 400) {

                    //echo memory_get_usage()." - Alloc memory \n";



                    $myBlockParam =  new EthBlockParam($i) ;


                    $block_latest = $eth->eth_getBlockByNumber($myBlockParam, $ethB);

                    //print_r($block_latest->toArray());
                    echo $block_latest->getProperty('hash')." - $counter block id =  $i\n";
                    //sleep(0.1);









                    foreach ($block_latest->transactions as $tx) {

$countTX = 0 ;


                        if (isset($tx->to)) {
                            //echo $tx->to->hexVal()." - tx should be a contract \n";

                            //echo $tx->to->hexVal()." ".$contract->getAddress() ."\n";






                            if (is_object($tx->to) && $tx->to->hexVal() == $contract->getAddress()) {
                                echo "weeeee FOUNDDDDD \n";
                                //$contract = $this->contracts[$tx->to->hexVal()];
                                $receipt = $eth->eth_getTransactionReceipt($tx->hash);


                                if (count($receipt->logs)) {

                                    foreach ($receipt->logs as $filterChange) {
                                        $event = $contract->processLog($filterChange);
                                        //var_dump($event);
                                        //die();
                                        if ($event->hasData()) {

                                            //var_dump($event);
                                            //var_dump($event->toArray());
                                            var_dump($event->getName());
                                        }
                                    }
                                }

                            }
                        }

                    }



                    unset($eth);
                   // unset($myBlockParam);
                   // unset($ethB);


                   $eth = new Ethereum($url);







                    $i--;
                    $counter++;
                }
                die("stop");

               $filterChange = new FilterChange(null,null);
                $filterChange->address = $contract;
                //$address = new
               $val = $contract->processLog($filterChange);





               echo $val->hexVal();


                print_r(($this->status($eth)));

                // Call a function.
                // Note Return type is D20 which is a the same as "address".
                $test = $register_drupal->validateUserByHash($hash);

                // Show results.
                echo "<p style='color: forestgreen;'>The Address submitted this hash is:<br />";
                echo $test->hexVal()."</p>";




            }
            catch (\Exception $exception) {
                echo "<p style='color: red;'>We have a problem:<br />";
                echo $exception->getMessage() . "</p>";
                echo "<pre>" . $exception->getTraceAsString() . "</pre>";
                die();
            }
            echo "<hr />";

        }

    }

    function status($eth) {
        $rows = [];

        /** @var Ethereum $eth */

        $rows[] = ['<b>JsonRPC standard Methods</b>', 'Read more about <a href="https://github.com/ethereum/wiki/wiki/JSON-RPC">Ethereum JsonRPC-API</a> implementation.'];

        $rows[] = ["Client version (web3_clientVersion)", $eth->web3_clientVersion()->val()];
        $rows[] = ["Listening (net_listening)", $eth->net_listening()->val() ? '✔' : '✘'];
        $rows[] = ["Peers (net_peerCount)", $eth->net_peerCount()->val()];
        $rows[] = ["Protocol version (eth_protocolVersion)", $eth->eth_protocolVersion()->val()];
        $rows[] = ["Network version (net_version)", $eth->net_version()->val()];
        $rows[] = ["Syncing (eth_syncing)", $eth->eth_syncing()->val() ? '✔' : '✘'];


        // Mining and Hashrate.
        $rows[] = ["Mining (eth_mining)", $eth->eth_mining()->val() ? '✔' : '✘'];

        $hash_rate = $eth->eth_hashrate();
        $mining = is_a($hash_rate, 'EthQ') ? ((int) ($hash_rate->val() / 1000) . ' KH/s') : '✘';
        $rows[] = ["Mining hashrate (eth_hashrate)", $mining];

        // Gas price is returned in WEI. See: http://ether.fund/tool/converter.
        $price = $eth->eth_gasPrice()->val();
        $price = $price . 'wei ( ≡ ' . number_format(($price / 1000000000000000000), 8, '.', '') . ' Ether)';
        $rows[] = ["Current price per gas in wei (eth_gasPrice)", $price];


        // Blocks.
        $rows[] = ["<b>Block info</b>", ''];
        $block_latest = $eth->eth_getBlockByNumber(new EthBlockParam('latest'), new EthB(FALSE));
        $rows[] = [
            "Latest block age",
            date(DATE_RFC850, $block_latest->getProperty('timestamp')),
        ];



        // Testing_only.

        $block_earliest = $eth->eth_getBlockByNumber(new EthBlockParam(1), new EthB(FALSE));
        $rows[] = [
            "Age of block number '1' <br/><small>The 'earliest' block has no timestamp on many networks.</small>",
            $block_earliest->getProperty('timestamp'),
        ];



        $rows[] = [
            "Client first (eth_getBlockByNumber('earliest'))",
            '<div style="max-width: 800px; max-height: 120px; overflow: scroll">' . $eth->debug('', $block_earliest) . '</div>',
        ];


        // Second param will return TX hashes instead of full TX.
        $block_latest = $eth->eth_getBlockByNumber(new EthBlockParam('earliest'), new EthB(FALSE));
        $rows[] = [
            "Client first (eth_getBlockByNumber('latest'))",
            '<div style="max-width: 800px; max-height: 120px; overflow: scroll">' . $eth->debug('', $block_latest) . '</div>',
        ];
        $rows[] = [
            "Uncles of latest block",
            '<div style="max-width: 800px; max-height: 120px; overflow: scroll">' . $eth->debug('', $block_latest->getProperty('uncles')) . '</div>',
        ];

        $high_block = $eth->eth_getBlockByNumber(new EthBlockParam(999999999), new EthB(FALSE));
        $rows[] = [
            "Get hash of a high block number<br /><small>Might be empty</small>",
            $high_block->getProperty('hash'),
        ];


        // Accounts.
        $rows[] = ["<b>Accounts info</b>", ''];
        $coin_base = $eth->eth_coinbase()->hexVal();
        if ($coin_base === '0x0000000000000000000000000000000000000000') {
            $coin_base = 'No coinbase available at this network node.';
        }

        $rows[] = ["Coinbase (eth_coinbase)", $coin_base];
        $address = ['No accounts available.'];
        $accounts = $eth->eth_accounts();
        if (count($accounts)) {
            $address = [];
            foreach ($eth->eth_accounts() as $addr) {
                $address[] = $addr->hexVal();
            }
        }
        $rows[] = ["Accounts (eth_accounts)", implode(', ', $address)];

        // More.
        $rows[] = [
            "web3_sha3('Hello World')",
            // Using the API would be: $eth->web3_sha3(new EthS('Hello World'))->hexVal(),
            $eth->sha3('Hello World'),
        ];

        // NON standard JsonRPC-API Methods below.
        $rows[] = ['<b>Non standard methods</b>', 'PHP Ethereum controller API provides additional methods. They are part of the <a href="https://github.com/digitaldonkey/ethereum-php">Ethereum PHP library</a>, but not part of JsonRPC-API standard.'];

        $rows[] = ["getMethodSignature('validateUserByHash(bytes32)')", $eth->getMethodSignature('validateUserByHash(bytes32)')];


    }







}