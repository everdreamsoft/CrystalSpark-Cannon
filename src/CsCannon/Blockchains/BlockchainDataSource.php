<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:55
 */

namespace CsCannon\Blockchains;


use SandraCore\ForeignEntityAdapter;

abstract class BlockchainDataSource
{

    public $sandra ;

    public abstract function getEvents($contract,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter ;

    public function __construct($sandra)
    {

        $this->sandra = $sandra ;

    }


    public function getBalance($contract,$limit,$offset):ForeignEntityAdapter{



       // $eventAdapter = new ForeignEntityAdapter($this->sandra,'',$this->sandra);
        return null ;

    }

}