<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Log;

class AjaxController extends Controller {

    /**
     * Process an API request (not necessarily Nutanix-specific)
     *
     * @param $address [The IP address of the cluster or CVM]
     * @param $port [The port to attempt the connection on]
     * @param $username [Credentials: username]
     * @param $password [Credentials: password]
     * @param $timeout [How long to wait until the request is considered failed]
     * @param $path [The path for the query itself]
     * @return mixed [JSON encoded response from the appropriate entity]
     */
    private function doApiRequest( $address, $port, $username, $password, $timeout, $path )
    {

        $client = new \GuzzleHttp\Client();

        $response = $client->get(
            sprintf( "https://%s:%s%s",
                $address,
                $port,
                $path
            ),
            [
                'config' => [
                    'curl' => [
                        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                        CURLOPT_USERPWD => $username . ':' . $password,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false
                    ],
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'verify' => false,
                    'timeout' => $timeout,
                    'connect_timeout' => $timeout
                ]
            ]
        );

        return( $response->json() );

    }

    /**
     * Build the main page's dashboard
     * This demo currently gets info about two 'static' items i.e. the cluster itself and the containers within that cluster
     *
     * @return mixed
     */
    public function buildDashboard()
    {

        try
        {

            $clusterInfo = $this->doApiRequest(
                $_POST['cluster_address'],
                $_POST['cluster_port'],
                $_POST['username'],
                $_POST['password'],
                $_POST['timeout'],
                '/PrismGateway/services/rest/v1/cluster/'
            );

//            $clusterInfo = $this->doApiRequest(
//                '10.10.10.30',
//                '9440',
//                'admin',
//                'admin',
//                3,
//                '/PrismGateway/services/rest/v1/cluster/'
//            );
//
//            dd( $clusterInfo );

            /* get some storage info so we can draw some graphs */
            $storage_info = [
                'ssd_used' => $clusterInfo['usageStats']['storage_tier.ssd.usage_bytes'],
                'ssd_free' => $clusterInfo['usageStats']['storage_tier.ssd.free_bytes'],
                'ssd_capacity' => $clusterInfo['usageStats']['storage_tier.ssd.capacity_bytes'],
                'hdd_used' => $clusterInfo['usageStats']['storage_tier.das-sata.usage_bytes'],
                'hdd_free' => $clusterInfo['usageStats']['storage_tier.das-sata.free_bytes'],
                'hdd_capacity' => $clusterInfo['usageStats']['storage_tier.das-sata.capacity_bytes'],
            ];

            $ssdChartData['data'][] = $storage_info['ssd_used'];
            $ssdChartData['labels'][] = 'SSD | Used';
            $ssdChartData['data'][] = $storage_info['ssd_capacity'];
            $ssdChartData['labels'][] = 'SSD | Capacity';

            /* free ssd space figures, in case they're needed
             $ssdChartData[ 'data' ][] = $storage_info[ 'ssd_free' ];
             $ssdChartData[ 'labels' ][] = 'SSD | Free';
            */

            $hddChartData['data'][] = $storage_info['hdd_used'];
            $hddChartData['labels'][] = 'HDD | Used';
            $hddChartData['data'][] = $storage_info['hdd_capacity'];
            $hddChartData['labels'][] = 'HDD | Capacity';

            /* free hdd space figures, in case they're needed
             $hddChartData[ 'data' ][] = $storage_info[ 'hdd_free' ];
             $hddChartData[ 'labels' ][] = 'HDD | Free';
            */

            /* create the graph images */
            $ssdGraph = time() . '-ssd.png';
            $hddGraph = time() . '-hdd.png';

            \ChrisRNutanix\APIDemo\Utilities\Chart::createPieChart($ssdChartData, $ssdGraph);
            \ChrisRNutanix\APIDemo\Utilities\Chart::createPieChart($hddChartData, $hddGraph);

            $containerInfo = $this->doApiRequest(
                $_POST['cluster_address'],
                $_POST['cluster_port'],
                $_POST['username'],
                $_POST['password'],
                $_POST['timeout'],
                '/PrismGateway/services/rest/v1/containers/'
            );

            $containers = array();

            foreach ($containerInfo['entities'] as $container) {
                $containers[] = [
                    'name' => $container['name'],
                    'replicationFactor' => $container['replicationFactor'],
                    'compressionEnabled' => $container['compressionEnabled'],
                    'compressionDelay' => $container['compressionDelayInSecs'],
                    'compressionSpaceSaved' => \ChrisRNutanix\APIDemo\Utilities\Formatting::humanFileSize($container['compressionSpaceSaved']),
                    'fingerprintOnWrite' => $container['fingerPrintOnWrite'] == 'on' ? true : false,
                    'onDiskDedup' => $container['onDiskDedup'] == 'OFF' ? true : false,
                ];
            }

            /* now that we've got all the info and arranged it nicely, return the JSON-formatted results back from the AJAX request */
            echo(json_encode(array(
                'result' => 'ok',
                'cluster-id' => $clusterInfo['id'],
                'cluster-name' => $clusterInfo['name'],
                'cluster-timezone' => $clusterInfo['timezone'],
                'cluster-numNodes' => $clusterInfo['numNodes'],
                'cluster-enableShadowClones' => $clusterInfo['enableShadowClones'] === true ? "Yes" : "No",
                'cluster-IP' => $clusterInfo['clusterExternalIPAddress'],
                'cluster-nosVersion' => $clusterInfo['version'],
                'hypervisorTypes' => $clusterInfo['hypervisorTypes'],
                'cluster-hasSED' => $clusterInfo['hasSelfEncryptingDrive'] ? 'Yes' : 'No',
                'ssdGraph' => $ssdGraph,
                'hddGraph' => $hddGraph,
                'containers' => $containers,
            )));

        }
        catch ( \GuzzleHttp\Exception\RequestException $e )
        {
            /* can't connect to CVM */
            echo( json_encode( array(
                'result' => 'failed',
                'message' => 'An error occurred while connecting to the CVM at address ' . $_POST['cluster_address'] . '.  Sorry about that.  Perhaps check your connection or credentials?'
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

    }

}