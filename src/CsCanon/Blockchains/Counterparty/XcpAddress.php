<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.03.2019
 * Time: 14:42
 */

namespace CsCanon\Blockchains\Counterparty;



use CsCanon\AssetFactory;
use CsCanon\Blockchains\Bitcoin\BitcoinAddress;
use SandraCore\EntityFactory;
use SandraCore\ForeignEntityAdapter;

class XcpAddress extends BitcoinAddress
{

    public static $isa = 'btcAddress';
    public static $file = 'btcAddressFile';
    public static  $className = 'App\XcpAddress' ;



    public function getBalance(){

        $system = app('Sandra')->getSandra();
       // dd($this->getAddress());

        //Xchain
        $foreignAdapter = new ForeignEntityAdapter("https://xchain.io/api/balances/".$this->getAddress(),'data',app('Sandra')->getSandra());

        //$foreignAdapter = new ForeignEntityAdapter("https://sandradev.everdreamsoft.com/activateTrigger.php?trigger=gameCenterApi&responseType=JSON&apik=18a48545-96cd-4e56-96aa-c8fcae302bfd&action=getUserAllEnv&mainAddress=".$this->getAddress(),'casaTookan/counterparty/eNCP',app('Sandra')->getSandra());
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

    public function getTransactions(){

        $sandra = app('Sandra')->getSandra();
        // dd($this->getAddress());

        //Xchain


        $transactionAdapter = new ForeignEntityAdapter($url, 'data', $sandra);
        $transactionAdapter->populate();
        print_r($transactionAdapter->return2dArray());

        return $this->returnBalance($foreignAdapter,$xcpTokenFactory);


        //return $reponse;





    }



    public function createForeign(){



        dd("creating foreign");



    }

    public function parseEDSPicassoResponse(ForeignEntityAdapter $foreignAdapter){

        $system = app('Sandra')->getSandra();


        $counterPartyAssetFactory = new EntityFactory("", "BooCollectionFile", $system);
        $counterPartyAssetFactory->populateLocal();

        dd($counterPartyAssetFactory->return2dArray());

        foreach ($foreignAdapter->return2dArray()as $key => $value){



            $getAssetEntity = $counterPartyAssetFactory->first('assetName',$value['f:asset']);
            if (!is_null($getAssetEntity))
            print_r($getAssetEntity->dumpMeta());


        }

        dd($counterPartyAssetFactory);



    }






}