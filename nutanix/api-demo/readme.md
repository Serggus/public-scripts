# Tiny Nutanix API Demo

## Prerequisites

This tiny demo is intended to be run on systems with PHP already installed.
If you don't have it (e.g. on a default Windows installation) you'll need to install PHP from http://php.net.

If you have a relatively recent Mac running OS X, you'll have PHP installed already (if you haven't removed it for some reason).

## Before starting

- Edit config.json & change the credentials, if necessary
- Edit config.json & set the CVM IP address/port
- To run on a web server, verify you have PHP >= 5.4 installed (required for PHP local web server)

## How to run - CLI

- From a terminal, change to the directory containing tiny-api-demo.php
- Run 'php ./tiny-api-demo.php'

... that's it.

## How to run - Web server

- From a terminal, change to the directory containing tiny-api-demo.php
- Run 'php -S localhost:8000'
- Browse to http://localhost:8000/tiny-api-demo.php

A successful example run against a Nutanix node running NOS 4.0.2.2 will look like this:

----- output starts -----

Tiny API demo

Cluster ID: 00050bf9-b19a-878c-0000-000000001522::5410
Cluster name: NTNX-Demo
Timezone: Australia/Melbourne
Nodes: 3
Shadow clones enabled?: No

etc ...

----- output ends -----