<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:50
 */

namespace App\Blockchains\Ethereum;


use App\Blockchains\BlockchainImporter;
use App\Blockchains\Counterparty\DataSource\XchainOnBcy;
use App\Blockchains\Ethereum\DataSource\phpWeb3;

class EthereumImporter extends BlockchainImporter
{

    public $defaultDataSource = phpWeb3::class ;
    public $blockchain = EthereumBlockchain::class ;




    public function getEvents($contract,$dataSource = 'default',$limit=null,$offset=null){



        $dataSource = $this->getDataSource($dataSource);

        $dataSource->getBalance('default',$limit,$offset);



        $foreignEntityEventsFactory = $dataSource->getEvents('default',$limit,$offset);
        echo"hello";
        die("was not me");

        $structure = $foreignEntityEventsFactory->return2dArray();
        $totalResponses['structure'] = reset($structure);



        $blockFactory = $this->getPopulatedBlockFactory($foreignEntityEventsFactory);
       // die();

        $addressFactory = $this->getPopulatedAddressFactory($foreignEntityEventsFactory);
        $contractFactory = $this->getPopulatedContractFactory($foreignEntityEventsFactory);



        $this->saveEvents($foreignEntityEventsFactory,$this->blockchain,$contractFactory,$addressFactory,$blockFactory);


       // $newAddress = count($addressFactory->newEntities);

        $totalResponses['data'] = $this->responseArray ;
        return $totalResponses ;



    }



}