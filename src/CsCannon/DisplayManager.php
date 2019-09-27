<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-27
 * Time: 18:57
 */

namespace CsCannon;


use CsCannon\Tests\Displayable;

class DisplayManager
{

    public $displayable ;
    public $outputFormat = 'json';

    public function __construct(Displayable $displayable)
    {

        $this->displayable = $displayable ;


    }

    public  function html(){



        $this->outputFormat = 'html' ;

    }

    public  function json(){

        $this->outputFormat = 'json' ;


    }

    public  function return(){





    }




}