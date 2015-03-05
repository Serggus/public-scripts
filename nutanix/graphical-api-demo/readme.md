# Graphical Nutanix API Demo

This small web application is an alternative to the [Tiny API Demo](https://github.com/digitalformula/public-scripts/tree/master/nutanix/api-demo) that demonstrates based text-based usage of the Nutanix API.

The graphical version shows a bit more information about the cluster used in the demo, compared to the [text version](https://github.com/digitalformula/public-scripts/tree/master/nutanix/api-demo).

## Prerequisites

- PHP >= 5.4 (required for local web server support)
- A connection to a Nutanix cluster
- Credentials for the relevant Nutanix cluster (read-only is fine)
- A recent web browser

## How to run - Web server

- From a terminal, change to the directory containing the demo files (look for index.php)
- Run 'php -S localhost:8000' (change the web server's port, if necessary)
- Browse to http://localhost:8000/ (and change the port, if you altered it in the previous step)

