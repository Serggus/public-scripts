<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
    // return $app->welcome();

    return view( 'home.index' );

//    try {
//        $temp = new App\APIRequest( 'viewonly', 'viewonly', '10.3.100.84', '9440', 3, '/PrismGateway/services/rest/v1/containers/' );
//        $result = $temp->doAPIRequest();
//        var_dump($result);
//    }
//    catch( Exception $e )
//    {
//        $message = $e->getMessage();
//        echo( $message );
//    }

});

$app->post( '/ajax/buildDashboard', 'App\Http\Controllers\AjaxController@buildDashboard' );