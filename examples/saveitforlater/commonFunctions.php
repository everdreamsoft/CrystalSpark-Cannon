<?php

function echoCode($codeString){

    echo "<pre>".PHP_EOL;
    echo $codeString.PHP_EOL ;
    echo "</pre>".PHP_EOL;

}

function echoArray($array){

     echoCode(print_r($array,1));

}

function echoTitle($titleString){

   echo "<h1> $titleString </h1>";

}

function echoSubTitle($titleString){

    echo "<h2> $titleString </h2>";

}

function echoExplanations($explanationString){

    echo "<p> $explanationString </p>";

}
