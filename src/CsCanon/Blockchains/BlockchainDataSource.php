<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:55
 */

namespace App\Blockchains;


use SandraCore\ForeignEntityAdapter;

class BlockchainDataSource
{

    public $sandra ;

    public function __construct($sandra)
    {

        $this->sandra = $sandra ;

    }

    public function getEvents($contract,$limit,$offset):ForeignEntityAdapter{



        $eventAdapter = new ForeignEntityAdapter($this->sandra,'',$this->sandra);
        return $eventAdapter ;

    }

    public function getBalance($contract,$limit,$offset):ForeignEntityAdapter{



        $eventAdapter = new ForeignEntityAdapter($this->sandra,'',$this->sandra);
        return $eventAdapter ;

    }

}