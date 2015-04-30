<?php

require '../vendor/autoload.php';
require '../libraries/chrisrnutanix/pChart.php';
require '../libraries/chrisrnutanix/chart.php';
require '../libraries/chrisrnutanix/functions.php';

/* fallback address is the IP of my demo block - set this to a default, if you want fallback to another IP when none is specified */
$cvm_address = $_POST[ 'cvm-address' ] != '' ? $_POST[ 'cvm-address' ] : '10.10.10.30';
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

    /* get some storage info so we can draw some graphs */
    $storage_info = [
        'ssd_used' => $json_data[ 'usageStats' ][ 'storage_tier.ssd.usage_bytes' ],
        'ssd_free' => $json_data[ 'usageStats' ][ 'storage_tier.ssd.free_bytes' ],
        'ssd_capacity' => $json_data[ 'usageStats' ][ 'storage_tier.ssd.capacity_bytes' ],
        'hdd_used' => $json_data[ 'usageStats' ][ 'storage_tier.das-sata.usage_bytes' ],
        'hdd_free' => $json_data[ 'usageStats' ][ 'storage_tier.das-sata.free_bytes' ],
        'hdd_capacity' => $json_data[ 'usageStats' ][ 'storage_tier.das-sata.capacity_bytes' ],
    ];

    $ssdChartData[ 'data' ][] = $storage_info[ 'ssd_used' ];
    $ssdChartData[ 'labels'][] = 'SSD | Used';
    $ssdChartData[ 'data' ][] = $storage_info[ 'ssd_capacity' ];
    $ssdChartData[ 'labels' ][] = 'SSD | Capacity';

    /* free ssd space figures, in case they're needed
     $ssdChartData[ 'data' ][] = $storage_info[ 'ssd_free' ];
     $ssdChartData[ 'labels' ][] = 'SSD | Free';
    */

    $hddChartData[ 'data' ][] = $storage_info[ 'hdd_used' ];
    $hddChartData[ 'labels'][] = 'HDD | Used';
    $hddChartData[ 'data' ][] = $storage_info[ 'hdd_capacity' ];
    $hddChartData[ 'labels' ][] = 'HDD | Capacity';

    /* free hdd space figures, in case they're needed
     $hddChartData[ 'data' ][] = $storage_info[ 'hdd_free' ];
     $hddChartData[ 'labels' ][] = 'HDD | Free';
    */

    /* create the graph images */
    $ssdGraph = time() . '-ssd.png';
    $hddGraph = time() . '-hdd.png';
    createPieChart( $ssdChartData, $ssdGraph );
    createPieChart( $hddChartData, $hddGraph );

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
        'numIOPS' => $json_data[ 'stats' ][ 'num_iops' ] == 0 ? "0 IOPS ... awwww!  :)" : $json_data[ 'stats' ][ 'num_iops' ] . ' IOPS',
        'ssdGraph' => $ssdGraph,
        'hddGraph' => $hddGraph,
    ) ) );

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