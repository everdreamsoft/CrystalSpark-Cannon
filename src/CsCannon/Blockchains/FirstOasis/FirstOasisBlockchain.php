<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 07.11.19
 * Time: 16:46
 */

namespace CsCannon\Blockchains\FirstOasis;


use CsCannon\Blockchains\Blockchain;

use CsCannon\Blockchains\Klaytn\GenericEventFactory;



class FirstOasisBlockchain extends Blockchain
{

    public function __construct()
    {

        $this->addressFactory = new FirstOasisAddressFactory();
        $this->contractFactory = new FirstOasisContractFactory();
        $this->eventFactory = new FirstOasisEventFactory();

    }

    protected $name = 'firstOasis';
    const NAME = 'FirstOasis';
    private static $staticBlockchain ;


    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new FirstOasisBlockchain();

        }

        return self::$staticBlockchain ;

    }

}