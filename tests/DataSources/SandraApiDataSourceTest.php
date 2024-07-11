<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 10.12.19
 * Time: 10:17
 */

use CsCannon\Blockchains\DataSource\SandraApiDataSource;

require_once __DIR__ . '/../../vendor/autoload.php'; // Autoload files using Composer autoload
require_once __DIR__ . '/DataSourceAbstract.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class SandraApiDataSourceTest extends DataSourceAbstract
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @throws Exception
     */
    public function test()
    {
        $bal = SandraApiDataSource::getBalance("0x7ad582f711a6bd5b9b50b2b18bc38a2aa652d4c3", 99999, 0);
    }

}
