<?php

require '../vendor/autoload.php';
require '../model/apiRequest.php';

/**
 * Check to see if a container name is already taken
 *
 * @param $containerName
 * @return bool
 */
function containerExists( $containerName )
{
    $checkContainerRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/containers/'
    );

    $containers = $checkContainerRequest->doAPIRequest( null, GET );
    if( $containers[ 'metadata' ][ 'grandTotalEntities' ] == 0 )
    {
        return false;
    }

    if( $containers[ 'metadata' ][ 'grandTotalEntities' ] > 0 )
    {
        foreach( $containers[ 'entities' ] as $container )
        {
            if( $container[ 'name' ] == $containerName )
            {
                return true;
            }
        }
        return false;
    }
}

try
{

    /* get some info about the storage pools in the cluster */
    $spInfoRequest = new apiRequest(
        $_POST[ 'cluster-username' ],
        $_POST[ 'cluster-password' ],
        $_POST[ 'cvm-address' ],
        $_POST[ 'cvm-port' ] != '' ? $_POST[ 'cvm-port' ] : '9440' ,
        3,
        '/PrismGateway/services/rest/v1/storage_pools/'
    );

    $storage_pools = $spInfoRequest->doAPIRequest( null, 'GET' );
    $spId = '';
    if( $storage_pools[ 'metadata' ][ 'grandTotalEntities' ] > 0 ) {

        /* A storage pool has been found - we can carry on */
        $spId = $storage_pools[ 'entities' ][ 0 ][ 'id' ];

        if( !containerExists( $_POST[ 'container-name' ] ) )
        {

            $parameters = [
                'name' => $_POST['container-name'],
                'storagePoolId' => $spId
            ];

            // $parameters = [ "name" => 'temp1', "storagePoolId" => $spId ];

            $createContainerRequest = new apiRequest(
                $_POST['cluster-username'],
                $_POST['cluster-password'],
                $_POST['cvm-address'],
                $_POST['cvm-port'] != '' ? $_POST['cvm-port'] : '9440',
                3,
                '/PrismGateway/services/rest/v1/containers/',
                'POST'
            );

            $response = $createContainerRequest->doAPIRequest( $parameters, 'POST');

            /* return a successful result */
            echo(json_encode(array(
                'result' => 'ok',
            )));

        }
        else
        {
            echo( json_encode( array(
                'result' => 'failed',
                'message' => 'That container name is already in use.&nbsp;&nbsp;:(&nbsp;&nbsp;Please specify a different container name, then try again.'
            )));
        }

    }
    else
    {
        echo( json_encode( array(
            'result' => 'failed',
            'message' => "No storage pools were found in this cluster.&nbsp;&nbsp;:(&nbsp;&nbsp;Please create at least one storage pool, then try again."
        )));
    }

}
catch ( GuzzleHttp\Exception\RequestException $e )
{
    /* can't connect to CVM */
    echo( json_encode( array(
        'result' => 'failed',
        'message' => "An error occurred while creating the container.  Please confirm that you can connect to the CVM at address " . $_POST[ 'cvm-address-container' ] . ' and that you have permissions to make cluster changes, then try again.'
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