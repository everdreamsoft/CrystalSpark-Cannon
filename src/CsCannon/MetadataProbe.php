<?php
/**
 * Created by EverdreamSoft.
 * User: Shaban Shaame
 * Date: 07.04.20
 * Time: 12:20
 */

namespace CsCannon;


use CsCannon\Blockchains\BlockchainContract;
use CsCannon\Blockchains\BlockchainContractStandard;
use SandraCore\DatabaseAdapter;
use SandraCore\Entity;
use SandraCore\ForeignEntityAdapter;

class MetadataProbe extends Entity
{

    private $nextAvailabilityTimestamp ;

    public function getProbeWaitTime(){

        return 1 ;

}

    public function getUrl(){


    return $this->get(MetadataProbeFactory::URL);

    }

    public function probe(BlockchainContractStandard ...$standards){

        $url =  $this->get(MetadataProbeFactory::URL);
        //$url =  'https://www.cryptovoxels.com/p/2500';

        $assetFactory = new AssetFactory();
        $tokenPathToAssetFactory = new \CsCannon\TokenPathToAssetFactory(SandraManager::getSandra());
        $iterationCount = 1 ;

        $probResult = array();

        foreach($standards as $standard) {
           if ($iterationCount > 1) sleep($this->getProbeWaitTime());

            $fetchedUrl = '';


            //now we replace the strings
            foreach($standard->specificatorData ? $standard->specificatorData : array() as $data => $dataValue ){

                $fetchedUrl = str_replace('{'.$data.'}',"$dataValue",$url);


            }

            //echo"Query at ".time().PHP_EOL;

            //This object was not intended to to this job but we use it anyways
            $foreignAdapter = new ForeignEntityAdapter($fetchedUrl,'',SandraManager::getSandra());
            $dataArray = $foreignAdapter->foreignRawArray;

            $dataArray[Asset::IMAGE_URL] = $dataArray['image'] ;
            $dataArray[Asset::METADATA_URL] = $url ;

            //if we have a cscannon asset name we use it at the key for the asset
            $assetId = $standard->getDisplayStructure();
            if ( isset($dataArray[Asset::CSCANNON_ID])){
                $assetId = $dataArray[Asset::CSCANNON_ID] ;
            }


            $collections = $this->getCollections();
            $contracts = $this->getContracts();
            $asset = $assetFactory->getOrCreate($assetId, $dataArray,$collections,$contracts);
            //then we update
            $assetFactory->assetUpdate($asset,$dataArray);
            $probeData = array();

            try {
                $firstContract = reset($contracts);
                /** @var BlockchainContract $firstContract */
                $firstContract->getId();
                $count = count($contracts) - 1;

                $assetFactory->assetUpdate($asset, $dataArray);
                $probeData['token'] = $standard->getDisplayStructure();
                $probeData['asset'] = $dataArray;
                $probeData['contract'] = $firstContract->getId();
                $probeData['otherContractCount'] = $count;



            }catch (\Exception $e){

                $probeData['error'] = $e->getMessage();
            }
            $probResult[] = $probeData ;



            $entToSolver = $tokenPathToAssetFactory->create($standard);
            $asset->bindToContractWithMultipleSpecifiers(reset($contracts),[$entToSolver]);
            $iterationCount++;

            $this->setProbeWait($this->getProbeWaitTime());




        }

        return $probResult ;

    }

    public function setProbeWait($seconds = 1){

        $now = time() ;
        $nextProbeIteration = $now + $seconds ;

       $waitTime =  $this->getOrInitReference(MetadataProbeFactory::TROTTLE_REQUEST,time()+$seconds);
       $this->setAvailabilityTimestamp(time()+$seconds);

       if ($waitTime->refValue <  $nextProbeIteration) $waitTime->refValue = $nextProbeIteration ;


    }

    public function getNextAvailability(){

        return $this->nextAvailabilityTimestamp ;


    }

    private function setAvailabilityTimestamp($time){

        if ($time > $this->getTrottleTime())
         $this->nextAvailabilityTimestamp = $time ;


    }

    public function getTrottleTime(){

        $dbLocalTime = $this->getReference(MetadataProbeFactory::TROTTLE_REQUEST)->refValue;
        if ($this->nextAvailabilityTimestamp < $dbLocalTime) $this->nextAvailabilityTimestamp = $dbLocalTime ;

        return  $this->nextAvailabilityTimestamp ;


    }

    public function isProbeReady(){

        $now = time() ;


        //hard reload
        $probeFactory = new MetadataProbeFactory();
        $probeFactory->populateLocal();
        $selfProbe = $probeFactory->last(MetadataProbeFactory::IDENTIFIER,$this->getId());
        $waitTime = $selfProbe->getReference(MetadataProbeFactory::TROTTLE_REQUEST);
        if (!$waitTime) return true ;

        $waitTime = $waitTime->refValue ;

        //$waitTime =  $this->getReference(MetadataProbeFactory::TROTTLE_REQUEST);
        $this->setAvailabilityTimestamp($waitTime);


        if ($waitTime >  $now) return false ;

        return true ;


    }

    public function queue(BlockchainContractStandard ...$standards){

        $search = array();

        foreach($standards as $standard) {
            $search[] = $standard->getDisplayStructure();
        }

        $tokenPathToAssetFactory = new TokenPathToAssetFactory(SandraManager::getSandra());
        $tokenPathToAssetFactory->populateFromSearchResults($search);

        //Now that we have all local existing we need to create missing tokenpath
        foreach ($standards as $standard){


            $exist = $tokenPathToAssetFactory->first(TokenPathToAssetFactory::ID,$standard->getDisplayStructure());

            if (!$exist){
                $exist = $tokenPathToAssetFactory->create($standard);


            }
           $data =  $standard->getSpecifierData();

            $json = BlockchainContractStandard::getJsonFromStandardArray([$standard]);
            $queuItem = $this->setBrotherEntity(MetadataProbeFactory::ON_QUEUE,$exist,false);
            $queuItem->setStorage($json,false);



        }


        DatabaseAdapter::commit();



    }

    public function getCollections():array{

        return $this->getJoinedEntities(MetadataProbeFactory::BIND_COLLECTION);

    }

    public function getContracts():array{

        return $this->getJoinedEntities(MetadataProbeFactory::BIND_CONTRACT);
    }


    public function executeQueue(){



        if (!$this->isProbeReady()) return true ; // there are potential element in the queue but the probe is not ready



        $queueItems = $this->getBrotherEntity(MetadataProbeFactory::ON_QUEUE);
        if (empty($queueItems))return false ;
        foreach ($queueItems ?? array() as $key => $queuItem){

            $json = $queuItem->getStorage();
            if (!$json) {unset($queueItems[$key]); continue ;}
            $standards[] = BlockchainContractStandard::getStandardsFromJson($json);
        }

        if (empty($standards))return false ;

        $firstOnQueue = reset($standards);
        $firstItem = reset($firstOnQueue);

        //echo"probing ".$firstItem->getDisplayStructure() .PHP_EOL ;



        if($this->probe($firstItem)){

            $firstQueueItem = reset($queueItems);
            $firstQueueItem->delete();
            $firstQueueItem->setStorage("");

        }

        return true ;



    }

    public function getId(){

        return $this->getReference(MetadataProbeFactory::IDENTIFIER)->refValue;

    }


}