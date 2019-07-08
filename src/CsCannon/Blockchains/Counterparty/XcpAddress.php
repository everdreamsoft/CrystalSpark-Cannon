<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCannon\Blockchains\Counterparty;



use CsCannon\AssetFactory;
use CsCannon\Blockchains\Bitcoin\BitcoinAddress;
use CsCannon\Blockchains\Blockchain;
use CsCannon\SandraManager;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntityAdapter;

class XcpAddress extends BitcoinAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    public static  $className = 'CsCannon\Blockchains\XcpAddress' ;



    public function getBalance(){

        $system = SandraManager::getSandra();
       // dd($this->getAddress());

        //Xchain
        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$this->getAddress(),'data',SandraManager::getSandra());

        //$foreignAdapter = new ForeignEntityAdapter("https://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&action=getUserAllEnv&mainAddress=".$this->getAddress(),'casaTookan/counterparty/eNCP',SandraManager::getSandra());
        //http://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&action=getEnvironments&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&apiv

        $xcpTokenFactory = new XcpTokenFactory();


        $foreignAdapter->adaptToLocalVocabulary(array('asset'=>'tokenId',
            'quantity'=>'balance'));
        $foreignAdapter->populate();

        //add counterparty data


       // $xcpTokenFactory->joinFactory()

        //$xcpTokenFactory->populateLocal();
       // dd($xcpTokenFactory->return2dArray());
        //$xcpTokenFactory->setFuseForeignOnRef('asset','tokenId',null);

       return $this->returnBalance($foreignAdapter,$xcpTokenFactory);


        //return $reponse;





}





    public  function getBlockchain():Blockchain{

        return new XcpBlockchain() ;


    }








}