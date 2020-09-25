<?php


/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 14:42
 */


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once '../config.php'; // Don't forget to configure your database in config.php
require_once '../viewHeader.html'; // Don't forget to configure your database in config.php


$catFactory = new \SandraCore\EntityFactory('cat','catFile',$sandra);

$catFactory->createNew(['name'=>'felix', 'age'=>'10','eyeColor'=>'blue']);


$catFactory = new \SandraCore\EntityFactory('cat','catFile',$sandra);
$catFactory->populateLocal();



foreach ($catFactory->sandraReferenceMap as $concept) {

    /** @var \SandraCore\Concept $concept  */

    $arrayOfColum[] = $concept->getShortname();


}
print_r($arrayOfColum);

print_r($catFactory->getDisplay('array'));

























