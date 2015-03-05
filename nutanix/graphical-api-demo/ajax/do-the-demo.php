<?php

require '../vendor/autoload.php';

$cvm_address = $_POST[ 'cvm-address' ];
$cvm_port = $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440';
$cluster_username = $_POST[ 'cluster-username' ] != '' ? $_POST[ 'cluster-username' ] : 'admin';
$cluster_password = $_POST[ 'cluster-password' ] != '' ? $_POST[ 'cluster-password' ] : 'admin';
$cluster_timeout = $_POST[ 'cluster-timeout' ] != '' ? $_POST[ 'cluster-timeout' ] : 10;

$client = new GuzzleHttp\Client();

try
{
    $response = $client->get(
        "https://$cvm_address:$cvm_port/PrismGateway/services/rest/v1/cluster/",
        [
            'config' => [
                'curl' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_USERPWD  => "$cluster_username:$cluster_password",
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'verify' => false,
                'timeout' => $cluster_timeout,
                'connect_timeout' => $cluster_timeout,
            ]
        ]
    );

    /* get the response data in JSON format */
    $json_data = $response->json();

    echo( json_encode( array(
        'result' => 'ok',
        'id' => $json_data[ 'id' ],
        'name' => $json_data[ 'name' ],
        'timezone' => $json_data[ 'timezone' ],
        'numNodes' => $json_data[ 'numNodes' ],
        'enableShadowClones' => $json_data[ 'enableShadowClones' ] === true ? "Yes" : "No",
        'blockZeroSn' => $json_data[ 'blockSerials' ][0],
        'clusterIP' => $json_data[ 'clusterExternalIPAddress' ],
        'nosVersion' => $json_data[ 'version' ],
        'hypervisorTypes' => $json_data[ 'hypervisorTypes' ],
        'hasSED' => $json_data[ 'hasSelfEncryptingDrive' ] ? "Yes": "No",
        'numIOPS' => $json_data[ 'stats' ][ 'num_iops' ] == 0 ? "0 ... awwww!  :)" : $json_data[ 'stats' ][ 'num_iops' ],
    ) ) );

}
catch ( GuzzleHttp\Exception\RequestException $e )
{
    /* Can't connect to CVM */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => "An error occurred while connecting to the CVM at address $cvm_address.  Sorry about that.  Perhaps check your connection or credentials?"
    )));
}
catch( Exception $e )
{
    /* Something else happened that we weren't prepared for ... */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => 'An unknown error has occurred.  Sorry about that.  Give it another go shortly.  :)'
    )));
}