<?php

/**
 * Created by PhpStorm.
 * User: chris
 * Date: 21/05/15
 * Time: 7:43 AM
 * Comment: This file exists for testing only and WILL have code that doesn't work!
 */

require 'vendor/autoload.php';
require 'model/apiRequest.php';

// working:
$parameters =
[
    "numVcpus" => "1",
    "name" => "ChrisTestVM7",
    "memoryMb" => "8192",
    "vmDisks" => [
        [
            "isCdrom" => "false",
            "vmDiskCreate" => [
                "size" => "42949672960",
                "containerId" => "00051771-d0ba-7f38-0000-000000001522::1041"
            ]
        ],
        [
            "isCdrom" => "false",
            "vmDiskCreate" => [
                "size" => "25949672960",
                "containerId" => "00051771-d0ba-7f38-0000-000000001522::1041"
            ]
        ]
    ]
];

$createVMRequest = new apiRequest(
    'admin',
    'admin',
    '10.10.10.30',
    '9440',
    3,
    '/api/nutanix/v0.8/vms/',
    'POST'
);

echo( 'This file is for testing only - there probably won\'t ever be anything here.&nbsp;&nbsp;:)' );