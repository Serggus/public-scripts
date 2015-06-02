<?php

require '../vendor/autoload.php';
require '../model/apiRequest.php';

/**
 * Check to see if a container name is already taken
 *
 * @param $containerName
 * @return bool
 */
function vmExists( $vmName )
{

    $checkVMRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/vms/'
    );

    $vms = $checkVMRequest->doAPIRequest( null, GET );

    if( $vms[ 'metadata' ][ 'grandTotalEntities' ] > 0 )
    {
        foreach( $vms[ 'entities' ] as $vm )
        {
            if( $vm[ 'vmName' ] == $vmName )
            {
                return true;
            }
        }
        return false;
    }
}

try
{

    /* get some info about the containers in the cluster */
    $containerInfoRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/containers/'
    );

    $containers = $containerInfoRequest->doAPIRequest( null, 'GET' );
    $containerId = '';
    if( $containers[ 'metadata' ][ 'grandTotalEntities' ] > 0 ) {

        /* A container has been found - we can carry on */
        $containerId = $containers[ 'entities' ][ 0 ][ 'id' ];

        if( !vmExists( $_POST[ 'server-name' ] ) )
        {

            $parameters = [];

            switch( $_POST[ 'server-profile' ] )
            {
                case 'exch':

                    $parameters = [
                        "numVcpus" => "2",
                        "name" => $_POST['server-name'],
                        "memoryMb" => "8192",
                        "vmDisks" => [
                            [
                                "isCdrom" => "false",
                                "vmDiskCreate" => [
                                    "size" => "128849018880",
                                    "containerId" => $containerId
                                ]
                            ],
                            [
                                "isCdrom" => "false",
                                "vmDiskCreate" => [
                                    "size" => "536870912000",
                                    "containerId" => $containerId
                                ]
                            ]
                        ]
                    ];

                    break;
                case 'dc':

                    $parameters = [
                        "numVcpus" => "1",
                        "name" => $_POST['server-name'],
                        "memoryMb" => "2048",
                        "vmDisks" => [
                            [
                                "isCdrom" => "false",
                                "vmDiskCreate" => [
                                    "size" => "268435456000",
                                    "containerId" => $containerId
                                ]
                            ]
                        ]
                    ];

                    break;
                case 'lamp':

                    $parameters = [
                        "numVcpus" => "1",
                        "name" => $_POST['server-name'],
                        "memoryMb" => "4096",
                        "vmDisks" => [
                            [
                                "isCdrom" => "false",
                                "vmDiskCreate" => [
                                    "size" => "42949672960",
                                    "containerId" => $containerId
                                ]
                            ]
                        ]
                    ];

                    break;
            }

            $createVMRequest = new apiRequest(
                $_POST['cluster-username'],
                $_POST['cluster-password'],
                $_POST['cvm-address'],
                $_POST['cvm-port'] != '' ? $_POST['cvm-port'] : '9440',
                3,
                '/api/nutanix/v0.8/vms/',
                'POST'
            );

            $response = $createVMRequest->doAPIRequest( $parameters, 'POST' );

            /* return a successful result */
            echo(json_encode(array(
                'result' => 'ok',
            )));

        }
        else
        {
            echo( json_encode( array(
                'result' => 'failed',
                'message' => 'That VM name is already in use.&nbsp;&nbsp;:(&nbsp;&nbsp;Please specify a different VM name, then try again.'
            )));
        }

    }
    else
    {
        echo( json_encode( array(
            'result' => 'failed',
            'message' => "No containers were found in this cluster.&nbsp;&nbsp;:(&nbsp;&nbsp;Please create at least one cluster, then try again."
        )));
    }

}
catch ( GuzzleHttp\Exception\RequestException $e )
{
    /* can't connect to CVM */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => "An error occurred while creating the VM.&nbsp;&nbsp;Please confirm that you can connect to the CVM at address " . $_POST[ 'cvm-address-container' ] . ' and that you have permissions to make cluster changes, then try again.'
    )));
}
catch( Exception $e )
{
    /* something else happened that we weren't prepared for ... */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => 'An unknown error has occurred.&nbsp;&nbsp;Sorry about that.&nbsp;&nbsp;Give it another go shortly.&nbsp;&nbsp;:)'
    )));
}