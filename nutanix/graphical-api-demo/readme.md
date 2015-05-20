# Graphical Nutanix API Demo

This small web application is an alternative to the [Tiny API Demo](https://github.com/digitalformula/public-scripts/tree/master/nutanix/api-demo) that demonstrates based text-based usage of the Nutanix API.

The graphical version shows a bit more information about the cluster used in the demo, compared to the [text version](https://github.com/digitalformula/public-scripts/tree/master/nutanix/api-demo).

## Prerequisites

- PHP >= 5.4 (required for local web server support)
- PHP with cURL support (required so the demo can read from a remote device) - MAMP is recommended
- A connection to a Nutanix cluster running NOS >= 4.1 (stats objects were different before 4.1)
- Credentials for the relevant Nutanix cluster (read-only is fine, unless you want to create a container)
- A recent web browser

## How to run - Web server

- From a terminal, change to the directory containing the demo files (look for index.php)
- If not using MAMP, run 'php -S localhost:8000' (change the web server's port, if necessary)
- Browse to http://localhost:8000/ (and change the port, if you altered it in the previous step)

## What will it do?

- Collect some basic info from the CVM or cluster IP address you specified
- Create a couple of PNG files in {app_home}/ajax/charts that show SSD & HDD utilisation
- Create a container, if you used that option

## Updates

2015.05.20

- Added ability to create a container

2015.05.01

- Container info added
- A bit of code cleanup, mostly in script.js for JSON response processing

2015.04.30

- Text storage removed
- Graphs added to show SSD and HDD tier usage vs capacity
- More of a 'dashboard' look, with high-level configuration info in the top panel

## Testing

This demo has been tested using NOS versions from 4.1 to 4.1.2.  It does *not* work with NOS 4.0.x anymore.  Sorry.