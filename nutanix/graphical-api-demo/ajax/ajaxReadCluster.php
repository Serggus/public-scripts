<?php

require '../vendor/autoload.php';
require '../libraries/chrisrnutanix/pChart.php';
require '../libraries/chrisrnutanix/pData.php';
require '../libraries/chrisrnutanix/chart.php';
require '../libraries/chrisrnutanix/functions.php';
require '../model/apiRequest.php';

try
{

    /* get some info about the containers available in the cluster */
    $containerInfoRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/containers/'
    );

    /* get the response data in JSON format */
    // $containerInfo = $containerInfoRequest->doAPIRequest2();
    $containerInfo = $containerInfoRequest->doAPIRequest( null, 'GET' );
    $containers = array();
    foreach( $containerInfo[ 'entities' ] as $container )
    {
        $containers[] = [
            'name' => $container[ 'name' ],
            'replicationFactor' => $container[ 'replicationFactor' ],
            'compressionEnabled' => $container[ 'compressionEnabled' ],
            'compressionDelay' => $container[ 'compressionDelayInSecs' ],
            'compressionSpaceSaved' => humanFileSize( $container[ 'compressionSpaceSaved' ] ),
            'fingerprintOnWrite' => $container[ 'fingerPrintOnWrite' ] == 'on' ? true : false,
            'onDiskDedup' => $container[ 'onDiskDedup' ] == 'OFF' ? true : false,
        ];
    }

    /* setup the request for the main cluster info query */
    $clusterInfoRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/cluster/'
    );

    /* get the response data in JSON format */
    $clusterInfo = $clusterInfoRequest->doAPIRequest( null, 'GET' );

    /* get some storage info so we can draw some graphs */
    $storage_info = [
        'ssd_used' => $clusterInfo[ 'usageStats' ][ 'storage_tier.ssd.usage_bytes' ],
        'ssd_free' => $clusterInfo[ 'usageStats' ][ 'storage_tier.ssd.free_bytes' ],
        'ssd_capacity' => $clusterInfo[ 'usageStats' ][ 'storage_tier.ssd.capacity_bytes' ],
        'hdd_used' => $clusterInfo[ 'usageStats' ][ 'storage_tier.das-sata.usage_bytes' ],
        'hdd_free' => $clusterInfo[ 'usageStats' ][ 'storage_tier.das-sata.free_bytes' ],
        'hdd_capacity' => $clusterInfo[ 'usageStats' ][ 'storage_tier.das-sata.capacity_bytes' ],
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

    /* now that we've got all the info and arranged it nicely, return the JSON-formatted results back from the AJAX request */
    echo( json_encode( array(
        'result' => 'ok',
        'cluster-id' => $clusterInfo[ 'id' ],
        'cluster-name' => $clusterInfo[ 'name' ],
        'cluster-timezone' => $clusterInfo[ 'timezone' ],
        'cluster-numNodes' => $clusterInfo[ 'numNodes' ],
        'cluster-enableShadowClones' => $clusterInfo[ 'enableShadowClones' ] === true ? "Yes" : "No",
        'cluster-blockZeroSn' => $clusterInfo[ 'blockSerials' ][0],
        'cluster-IP' => $clusterInfo[ 'clusterExternalIPAddress' ],
        'cluster-nosVersion' => $clusterInfo[ 'version' ],
        'hypervisorTypes' => $clusterInfo[ 'hypervisorTypes' ],
        'cluster-hasSED' => $clusterInfo[ 'hasSelfEncryptingDrive' ] ? 'Yes' : 'No',
        'cluster-numIOPS' => $clusterInfo[ 'stats' ][ 'num_iops' ] == 0 ? '0 IOPS ... awwww!  :)' : $clusterInfo[ 'stats' ][ 'num_iops' ] . ' IOPS',
        'ssdGraph' => $ssdGraph,
        'hddGraph' => $hddGraph,
        'containers' => $containers,
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