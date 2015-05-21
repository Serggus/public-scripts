<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 21/05/15
 * Time: 7:43 AM
 */

require 'vendor/autoload.php';
require 'model/apiRequest.php';

$parameters = ["name" => 'temp', "storagePoolId" => '00051658-4333-8ae4-0000-000000001522::7090'];
$parameters = [];

$temp = doAPIPostRequest( $parameters );
var_dump( $temp );

function doAPIPostRequest( $parameters = null )
{

    $client = new GuzzleHttp\Client();

    $request = $client->createRequest(
        'GET',
        "https://10.10.10.30:9440/PrismGateway/services/rest/v1/containers/",
        [
            'config' => [
                'curl' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD => 'admin:admin',
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ],
                'verify' => false,
                'timeout' => 3,
                'connect_timeout' => 3,
            ],
            'headers' => [
                "Accept" => "application/json",
                "Content-Type" => "application/json"
            ],
            'body' => json_encode( $parameters )
        ]
    );

    $response = $client->send($request);

    /* return the response data in JSON format */
    return( $response->json() );

}