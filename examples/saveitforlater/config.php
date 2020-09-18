<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 18.09.20
 * Time: 17:28
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

//step 1 create a database for example cscannon

//step 2 configure your db credentials
$dbName = 'cscannon';

$dbUsername = 'root';
$dbpassword = '';
$dbHost = '127.0.0.1';
//Step 3 Chose any name as your database prefix. You can have multiple datagraph instance on the same db
$envName = 'myCannon';

//end of configuration

//lets try to connect
try {
    $sandra = new \SandraCore\System($envName, true, $dbHost, $dbName, $dbUsername, $dbpassword);
    \CsCannon\SandraManager::setSandra($sandra); //pass your sandra entity to CSCannon




}catch (Exception $exception){

    echo "We couldn't connect to database error is the following".PHP_EOL;
    echo "$exception";
    //Ok we have to stop before we do any more harm
    die();



}




