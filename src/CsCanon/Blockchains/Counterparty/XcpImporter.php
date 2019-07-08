<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:50
 */

namespace CsCanon\Blockchains\Counterparty;


use CsCanon\Blockchains\BlockchainImporter;
use CsCanon\Blockchains\Counterparty\DataSource\XchainOnBcy;

class XcpImporter extends BlockchainImporter
{

    public $defaultDataSource = XchainOnBcy::class ;
    public $blockchain = XcpBlockchain::class ;




    public function getEvents($contract,$dataSource = 'default',$limit=null,$offset=null){

        $dataSource = $this->getDataSource($dataSource);



        $foreignEntityEventsFactory = $dataSource->getEvents('default',$limit,$offset);

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