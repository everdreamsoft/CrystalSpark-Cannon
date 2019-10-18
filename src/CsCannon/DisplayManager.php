<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-27
 * Time: 18:57
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainEventFactory;
use CsCannon\Displayable;

class DisplayManager
{

    public $displayable ;
    public $outputFormat = 'json';
    public $returnData = array();
    public $dataStore = array();


    public function __construct(Displayable $displayable)
    {

        $this->displayable = $displayable ;


    }

    public  function html():self{



        $this->outputFormat = 'html' ;
        return $this ;

    }

    public  function buildHeaders(){





    }


    public  function json():self{

        $this->outputFormat = 'json' ;
        return $this ;


    }

    public  function pushData(array $data){

        $this->returnData =$this->returnData + $data;
        return $this ;


    }




    public  function return() {

        $this->returnData = $this->displayable->returnArray($this);



        return $this->returnData ;





    }




}