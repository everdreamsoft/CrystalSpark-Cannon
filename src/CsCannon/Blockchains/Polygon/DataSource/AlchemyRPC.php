<?php

namespace CsCannon\Blockchains\Polygon\DataSource;

/**
 * Created by EverdreamSoft.
 * User: Ranjit
 * Date: 03.21.24
 * Time: 09:55
 */
class AlchemyRPC
{

    public static function getTransactionByHash($transactionHash, $network, $key)
    {
        $url = "https://" . $network . ".g.alchemy.com/v2/" . $key;
        $data = array(
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionByHash",
            "params" => [
                $transactionHash
            ]
        );
        return self::curl($url, $data);
    }

    public static function getTransactionReceipt($transactionHash, $network, $key)
    {
        $url = "https://" . $network . ".g.alchemy.com/v2/" . $key;
        $data = array(
            "id" => 1,
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionReceipt",
            "params" => [
                $transactionHash
            ]
        );
        return self::curl($url, $data);
    }

    private static function curl($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'accept: application/json',
            'content-type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, 1);
    }

}

