<?php

/**
 * Information about the current API request
 *
 * Class apiRequest
 */
class apiRequest
{

    /**
     * The username to use during the connection
     */
    var $username;

    /**
     * The password to use during the connection
     */
    var $password;

    /**
     * The path for the actual API request
     */
    var $requestPath;

    /**
     * The IP address of the CVM
     */
    var $cvmAddress;

    /**
     * The port to connect on
     */
    var $cvmPort;

    /**
     * The timeout period i.e. how long to wait before the request is considered failed
     */
    var $connectionTimeout;

    /**
     * @param string $username
     * @param string $password
     * @param $requestPath
     * @param $cvmAddress
     * @param string $cvmPort
     * @param int $connectionTimeout
     */
    public function __construct( $username = 'admin', $password ='admin', $cvmAddress, $cvmPort = '9440', $connectionTimeout = 3, $requestPath )
    {
        $this->username = $username;
        $this->password = $password;
        $this->requestPath = $requestPath;
        $this->cvmAddress = $cvmAddress;
        $this->cvmPort = $cvmPort;
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * Process the API request based on the current apiRequest instance
     *
     * @return mixed
     */
    public function doAPIRequest()
    {

        $client = new GuzzleHttp\Client();

        $response = $client->get(
            sprintf( "https://%s:%s%s",
                $this->cvmAddress,
                $this->cvmPort,
                $this->requestPath
                ),
            [
                'config' => [
                    'curl' => [
                        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                        CURLOPT_USERPWD  => $this->username . ':' . $this->password,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false
                    ],
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'verify' => false,
                    'timeout' => $this->connectionTimeout,
                    'connect_timeout' => $this->connectionTimeout,
                ]
            ]
        );

        /* return the response data in JSON format */
        return( $response->json() );

    }

}