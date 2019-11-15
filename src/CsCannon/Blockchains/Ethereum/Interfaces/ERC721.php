<?php

namespace CsCannon\Blockchains\Ethereum\Interfaces;
use CsCannon\AssetSolvers\DefaultEthereumSolver;
use CsCannon\Blockchains\BlockchainContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractFactory;
use CsCannon\Blockchains\Ethereum\EthereumContractStandard;
use CsCannon\Orb;
use SandraCore\Reference;
use SandraCore\System;

/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 21.05.19
 * Time: 14:40
 */
class ERC721 extends EthereumContractStandard
{

    public $tokenId = null ;
    public $specificatorArray = ['tokenId'];

    public function __construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget,System $system)
    {
        $this->solver = DefaultEthereumSolver::class ;

        parent::__construct($sandraConcept,$sandraReferencesArray,$factory,$entityId,$conceptVerb,$conceptTarget, $system);





    }




    public function setTokenId($tokenId)
    {
        return $this->setTokenPath(array('tokenId'=>$tokenId));

    }

    //ovveride the method to catch tokenId
    public function setTokenPath($tokenPath){

        $tokenIdUnid = $this->system->systemConcept->get('tokenId');

        //we check if we got a raw value or a reference

        if (isset ($tokenPath[$tokenIdUnid])){

            $referenceConceptOrString = $tokenPath[$tokenIdUnid];
            if ($referenceConceptOrString instanceof Reference){
                $referenceConceptOrString = $referenceConceptOrString->refValue;
            }



            $this->tokenId = $referenceConceptOrString;


        }

        if (isset ($tokenPath['tokenId'])){

            $referenceConceptOrString = $tokenPath['tokenId'];
            if ($referenceConceptOrString instanceof Reference){
                $referenceConceptOrString = $referenceConceptOrString->refValue;
            }

            $this->tokenId = $referenceConceptOrString;
        }


       return parent::setTokenPath($tokenPath);

    }



    public function getStandardName()
    {
       return "ERC721";
    }

    public static function init($tokenId=null)
    {


        $directTokenId = null ;
        $tokenData = null ;

        //if the user only send a string then he wants to init with the token id
        if (is_string($tokenId) or is_int($tokenId)){
            $tokenData = null ; //we remove token data array
            $directTokenId = $tokenId ;
        }
        if (is_array($tokenId)) $tokenData = $tokenId ;


        $return = parent::init($tokenData);
        if($directTokenId)  $return->setTokenId($directTokenId); // then we set token id afterwards

        return $return ;



    }

    public function resolveAsset(Orb $orb)
    {

       $return = DefaultEthereumSolver::resolveAsset($orb,$this);
       return $return ;
    }


    public function getDisplayStructure()
    {

       $return = 'tokenId-'.$this->tokenId ;
        return $return ;
    }

    public function getInterfaceAbi()
    {

        $strJsonFileContents = "[
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"interfaceId\",
          \"type\": \"bytes4\"
        }
      ],
      \"name\": \"supportsInterface\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"bool\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [],
      \"name\": \"name\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"string\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"getApproved\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"address\"
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
          \"name\": \"to\",
          \"type\": \"address\"
        },
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"approve\",
      \"outputs\": [],
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
          \"name\": \"from\",
          \"type\": \"address\"
        },
        {
          \"name\": \"to\",
          \"type\": \"address\"
        },
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"transferFrom\",
      \"outputs\": [],
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
          \"name\": \"index\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"tokenOfOwnerByIndex\",
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
          \"name\": \"from\",
          \"type\": \"address\"
        },
        {
          \"name\": \"to\",
          \"type\": \"address\"
        },
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"safeTransferFrom\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"index\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"tokenByIndex\",
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
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"ownerOf\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"address\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"owner\",
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
      \"inputs\": [],
      \"name\": \"renounceOwnership\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [],
      \"name\": \"owner\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"address\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [],
      \"name\": \"isOwner\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"bool\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [],
      \"name\": \"symbol\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"string\"
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
          \"name\": \"to\",
          \"type\": \"address\"
        },
        {
          \"name\": \"approved\",
          \"type\": \"bool\"
        }
      ],
      \"name\": \"setApprovalForAll\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": false,
      \"inputs\": [
        {
          \"name\": \"from\",
          \"type\": \"address\"
        },
        {
          \"name\": \"to\",
          \"type\": \"address\"
        },
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        },
        {
          \"name\": \"_data\",
          \"type\": \"bytes\"
        }
      ],
      \"name\": \"safeTransferFrom\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"tokenURI\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"string\"
        }
      ],
      \"payable\": false,
      \"stateMutability\": \"view\",
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
          \"name\": \"operator\",
          \"type\": \"address\"
        }
      ],
      \"name\": \"isApprovedForAll\",
      \"outputs\": [
        {
          \"name\": \"\",
          \"type\": \"bool\"
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
          \"name\": \"newOwner\",
          \"type\": \"address\"
        }
      ],
      \"name\": \"transferOwnership\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"inputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"constructor\"
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
          \"indexed\": true,
          \"name\": \"tokenId\",
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
          \"name\": \"approved\",
          \"type\": \"address\"
        },
        {
          \"indexed\": true,
          \"name\": \"tokenId\",
          \"type\": \"uint256\"
        }
      ],
      \"name\": \"Approval\",
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
          \"name\": \"operator\",
          \"type\": \"address\"
        },
        {
          \"indexed\": false,
          \"name\": \"approved\",
          \"type\": \"bool\"
        }
      ],
      \"name\": \"ApprovalForAll\",
      \"type\": \"event\"
    },
    {
      \"anonymous\": false,
      \"inputs\": [
        {
          \"indexed\": true,
          \"name\": \"previousOwner\",
          \"type\": \"address\"
        },
        {
          \"indexed\": true,
          \"name\": \"newOwner\",
          \"type\": \"address\"
        }
      ],
      \"name\": \"OwnershipTransferred\",
      \"type\": \"event\"
    },
    {
      \"constant\": false,
      \"inputs\": [
        {
          \"name\": \"userAddress\",
          \"type\": \"address\"
        },
        {
          \"name\": \"uidHash\",
          \"type\": \"bytes32\"
        },
        {
          \"name\": \"googleHash\",
          \"type\": \"bytes32\"
        },
        {
          \"name\": \"nonce\",
          \"type\": \"uint64\"
        }
      ],
      \"name\": \"registerUser\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": false,
      \"inputs\": [
        {
          \"name\": \"userAddress\",
          \"type\": \"address\"
        },
        {
          \"name\": \"newLevel\",
          \"type\": \"uint64\"
        }
      ],
      \"name\": \"updateUserLevel\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": false,
      \"inputs\": [
        {
          \"name\": \"userAddress\",
          \"type\": \"address\"
        },
        {
          \"name\": \"newCertificationLevel\",
          \"type\": \"uint64\"
        }
      ],
      \"name\": \"updateUserCertificationLevel\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    },
    {
      \"constant\": true,
      \"inputs\": [
        {
          \"name\": \"userAddress\",
          \"type\": \"address\"
        }
      ],
      \"name\": \"getUser\",
      \"outputs\": [
        {
          \"name\": \"nonce\",
          \"type\": \"uint64\"
        },
        {
          \"name\": \"level\",
          \"type\": \"uint64\"
        },
        {
          \"name\": \"certificationLevel\",
          \"type\": \"uint64\"
        },
        {
          \"name\": \"uidHash\",
          \"type\": \"bytes32\"
        },
        {
          \"name\": \"googleHash\",
          \"type\": \"bytes32\"
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
          \"name\": \"userAddress\",
          \"type\": \"address\"
        }
      ],
      \"name\": \"resetUser\",
      \"outputs\": [],
      \"payable\": false,
      \"stateMutability\": \"nonpayable\",
      \"type\": \"function\"
    }
  ]";
        return $strJsonFileContents ;
    }


}