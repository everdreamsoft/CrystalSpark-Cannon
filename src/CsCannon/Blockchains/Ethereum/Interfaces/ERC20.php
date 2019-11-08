<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\AssetSolvers\LocalSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class ERC20 extends EthereumContractStandard
{


    public $specificatorArray = null ;

    public function __construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget,System $system)
    {
        $this->solver = LocalSolver::class ;

        parent::__construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget, $system);

    }




    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){



       parent::setTokenPath($tokenPath);

    }



    public function getStandardName()
    {
       return "ERC20";
    }

    public function getSolver()
    {

    }

    public function resolveAsset(Orb $orb)
    {

       $return = DefaultEthereumSolver::resolveAsset($orb,$this);
       return $return ;
    }


    public function getDisplayStructure()
    {

       $return = null ;
        return $return ;
    }

    public function getInterfaceAbi()
    {

        $strJsonFileContents = "[
	{
        \"constant\": false,
		\"inputs\": [
			{
				\"name\": \"spender\",
				\"type\": \"address\"
			},
			{
				\"name\": \"amount\",
				\"type\": \"uint256\"
			}
		],
		\"name\": \"approve\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"bool\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"nonpayable\",
		\"type\": \"function\"
	},
	{
		\"constant\": true,
		\"inputs\": [],
		\"name\": \"totalSupply\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"uint256\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"view\",
		\"type\": \"function\"
	},
	{
		\"constant\": false,
		\"inputs\": [
			{
				\"name\": \"sender\",
				\"type\": \"address\"
			},
			{
				\"name\": \"recipient\",
				\"type\": \"address\"
			},
			{
				\"name\": \"amount\",
				\"type\": \"uint256\"
			}
		],
		\"name\": \"transferFrom\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"bool\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"nonpayable\",
		\"type\": \"function\"
	},
	{
		\"constant\": true,
		\"inputs\": [
			{
				\"name\": \"account\",
				\"type\": \"address\"
			}
		],
		\"name\": \"balanceOf\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"uint256\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"view\",
		\"type\": \"function\"
	},
	{
		\"constant\": false,
		\"inputs\": [
			{
				\"name\": \"recipient\",
				\"type\": \"address\"
			},
			{
				\"name\": \"amount\",
				\"type\": \"uint256\"
			}
		],
		\"name\": \"transfer\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"bool\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"nonpayable\",
		\"type\": \"function\"
	},
	{
		\"constant\": true,
		\"inputs\": [
			{
				\"name\": \"owner\",
				\"type\": \"address\"
			},
			{
				\"name\": \"spender\",
				\"type\": \"address\"
			}
		],
		\"name\": \"allowance\",
		\"outputs\": [
			{
				\"name\": \"\",
				\"type\": \"uint256\"
			}
		],
		\"payable\": false,
		\"stateMutability\": \"view\",
		\"type\": \"function\"
	},
	{
		\"anonymous\": false,
		\"inputs\": [
			{
				\"indexed\": true,
				\"name\": \"from\",
				\"type\": \"address\"
			},
			{
				\"indexed\": true,
				\"name\": \"to\",
				\"type\": \"address\"
			},
			{
				\"indexed\": false,
				\"name\": \"value\",
				\"type\": \"uint256\"
			}
		],
		\"name\": \"Transfer\",
		\"type\": \"event\"
	},
	{
		\"anonymous\": false,
		\"inputs\": [
			{
				\"indexed\": true,
				\"name\": \"owner\",
				\"type\": \"address\"
			},
			{
				\"indexed\": true,
				\"name\": \"spender\",
				\"type\": \"address\"
			},
			{
				\"indexed\": false,
				\"name\": \"value\",
				\"type\": \"uint256\"
			}
		],
		\"name\": \"Approval\",
		\"type\": \"event\"
	}
]";
        return $strJsonFileContents ;
    }


}