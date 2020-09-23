<?php

function echoCode($codeString){

    echo "<pre><code class='language-php' data-lang='php'>".PHP_EOL;
    echo $codeString.PHP_EOL ;
    echo "</code></pre>".PHP_EOL;

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

function buildTd($columContent){

    return "<td> $columContent </td>";

}

function buildTr($lineContent){

    return "<tr> $lineContent </tr>";

}

function echoHTMLTable($tableData,array $headArray=null){

    $headHtml = '';
    if (is_array($headArray)){
        foreach ($headArray as $headName)
        $headHtml .= "<th> $headName </th>";


    }

    echo "<table class='table'>$headHtml. $tableData </table>";

}
