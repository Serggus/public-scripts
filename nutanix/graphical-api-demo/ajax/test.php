<?php

require '../vendor/autoload.php';
require '../libraries/chrisrnutanix/pChart.php';
require '../libraries/chrisrnutanix/chart.php';
require '../libraries/chrisrnutanix/functions.php';
require '../model/apiRequest.php';

try
{

    /* setup the request for the main cluster info query */
    $clusterInfoRequest = new apiRequest(
        'admin',
        'admin',
        '10.10.10.30',
        '9440',
        3,
        // '/PrismGateway/services/rest/v1/health_checks/2001'
        '/PrismGateway/services/rest/v1/license'
    );

    /* get the response data in JSON format */
    $clusterInfo = $clusterInfoRequest->doAPIRequest();

    var_dump( $clusterInfo );
    die();

//    foreach( $clusterInfo as $check )
//    {
//        foreach( $check[ 'metrics' ] as $entity )
//        {
//            var_dump( $entity );
//            echo( '<br>' );
//        }
//    }

}
catch ( GuzzleHttp\Exception\RequestException $e )
{
    /* can't connect to CVM */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => "An error occurred while connecting to the CVM at address $cvm_address.  Sorry about that.  Perhaps check your connection or credentials?"
    )));
}
catch( Exception $e )
{
    /* something else happened that we weren't prepared for ... */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => 'An unknown error has occurred.  Sorry about that.  Give it another go shortly.  :)'
    )));
}