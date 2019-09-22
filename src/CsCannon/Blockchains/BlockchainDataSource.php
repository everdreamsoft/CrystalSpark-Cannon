<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 06.06.19
 * Time: 09:55
 */

namespace CsCannon\Blockchains;


use CsCannon\AssetCollection;
use CsCannon\AssetCollectionFactory;
use CsCannon\Balance;
use SandraCore\ForeignEntityAdapter;

abstract class BlockchainDataSource
{

    public $sandra ;
    protected $localCollections = null ;

    public abstract function getEvents($contract,$batchMax=1000,$offset=0,$address=null):ForeignEntityAdapter ;

    public function __construct($sandra, AssetCollectionFactory $localCollections = null)
    {

        $this->sandra = $sandra ;
        $this->localCollections = $localCollections ;

    }


    public abstract function getBalance(BlockchainAddress $address,$limit,$offset):Balance ;

}