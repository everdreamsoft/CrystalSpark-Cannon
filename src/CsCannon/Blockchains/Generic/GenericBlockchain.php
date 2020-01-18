<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 07.11.19
 * Time: 16:46
 */

namespace CsCannon\Blockchains\Generic;


use CsCannon\Blockchains\Blockchain;
use CsCannon\Blockchains\Generic\GenericEventFactory;


class GenericBlockchain extends Blockchain
{

    public function __construct()
    {

        $this->addressFactory = new GenericAddressFactory();
        $this->contractFactory = new GenericContractFactory();
        $this->eventFactory = new GenericEventFactory();

    }

    protected $name = 'generic';
    const NAME = 'generic';
    private static $staticBlockchain ;


    public static function getStatic()
    {

        if (is_null(self::$staticBlockchain)){
            self::$staticBlockchain = new GenericBlockchain();

        }

        return self::$staticBlockchain ;

    }

}