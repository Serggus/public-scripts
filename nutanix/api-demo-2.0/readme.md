# Graphical Nutanix API Demo v2.0

This small web application is a small read-only demo of the Nutanix API.  It shows how a small custom dashboard can be used to display Nutanix cluster information.

## Prerequisites

- A workstation or laptop configured as a development environment.  This is the most important requirement as you won't be able to connect to any cluster or CVM without the php5-curl library working properly.
- PHP >= 5.4 (required for local web server support)
- A connection to a Nutanix cluster running NOS >= 4.1 (stats objects were different before 4.1)
- Credentials for the relevant Nutanix cluster (read-only is fine)
- A recent web browser

## How to run - Web server

- From a terminal, change to the directory containing the demo files (look for index.php)
- Run 'php -S localhost:8000' (change the web server's port, if necessary)
- Browse to http://localhost:8000/ (and change the port, if you altered it in the previous step)

## What will it do?

- Collect some basic info from the CVM or cluster IP address you specified
- Create a couple of PNG files in {app_home}/ajax/charts that show SSD & HDD utilisation

## Updates

2015.05.04

- Move to Laravel Lumen framework - better performance and more options for extending demo application later
- Removed cluster IOPS from the display (it was wrong ... oops)

2015.05.01

- Container info added
- A bit of code cleanup, mostly in script.js for JSON response processing

2015.04.30

- Text storage removed
- Graphs added to show SSD and HDD tier usage vs capacity
- More of a 'dashboard' look, with high-level configuration info in the top panel

## Testing

This demo has been tested using NOS versions from 4.1 to 4.1.2.  It does *not* fully work with NOS 4.0.x anymore (disk space graphs will fail).  Sorry.