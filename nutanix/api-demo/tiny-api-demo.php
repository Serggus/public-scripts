<?php

/**
	*
	* There is limited error-checking in this script
	* If you need to change things you'll need to mess about with the values that are displayed
	*
	* Testing only!  :)
	*
	* Thanks,
	* Chris R
	*
*/

/**
	*
	* 2015.02.03 PST
	* The default CVM settings are for demo cluster
	*
	* VPN connection required if you're using a Nutanix internal demo cluster
	*
*/

require 'vendor/autoload.php';

/* grab the config */
$config = json_decode( file_get_contents( __DIR__ . '/config.json' ) );

/* make sure the config was loaded successfully */
if( $config )
{
	/* are we running in the CLI or via a web server? */
	$newline = '';
	if( isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) && php_sapi_name() != 'CLI' )
	{
		$newline = '<br>';
	}
	else
	{
		$newline = "\n\r";
	}

	$client = new GuzzleHttp\Client();

	try
	{
		$response = $client->get(
			"https://$config->cvm_address:$config->cvm_port/PrismGateway/services/rest/v1/cluster/",
			[
				'config' => [
					'curl' => [
						CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
						CURLOPT_USERPWD  => "$config->username:$config->password",
						CURLOPT_SSL_VERIFYHOST => false,
						CURLOPT_SSL_VERIFYPEER => false
						],
					'headers' => [
						'Accept' => 'application/json'
					],
				'verify' => false,
				'timeout' => $config->timeout,
				'connect_timeout' => $config->timeout,
				]
			]
		);

		/* get the response data in JSON format */
		$json_data = $response->json();

		/* display a simple table with some basic cluster info */
		echo(
				sprintf(
						"{$newline}Tiny API demo{$newline}{$newline}Cluster ID: %s{$newline}Cluster name: %s{$newline}Timezone: %s{$newline}Nodes: %s{$newline}Shadow clones enabled? %s{$newline}{$newline}etc ...",
						$json_data[ 'id' ],
						$json_data[ 'name' ],
						$json_data[ 'timezone' ],
						$json_data[ 'numNodes' ],
						$json_data[ 'enableShadowClones' ] === true ? "Yes" : "No"
						)
		);

	}
	catch ( GuzzleHttp\Exception\RequestException $e )
	{
		/* can't connect to CVM */
		echo( "{$newline}An error occurred while connecting to the CVM at address $cvm.  Sorry about that.  Perhaps check your connection or credentials?  :)" );
	}
	catch( Exception $e )
	{
		/* something else happened that we weren't prepared for ... */
		echo( "{$newline}An unknown error has occurred.  Sorry about that.  Give it another go shortly.  :)" );
	}
}
else
{
	/* there was an error loading the configuration */
	echo( "{$newline}An error occurred while loading the configuration.  Sorry about that.  Check config.json and give it another go.  :)" );
}

echo( "{$newline}{$newline}" );

?>