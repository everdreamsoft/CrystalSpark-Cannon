<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 2019-09-27
 * Time: 19:12
 */

namespace CsCannon;


 use CsCannon\DisplayManager;

 Interface Displayable
{

    public function returnArray(DisplayManager $display);
     public function display():DisplayManager;



}