# Nutanix Scripts

The scripts in this folder relate to Nutanix management.

Intended to be reference or dev/test use only.  Please DO NOT use these scripts in production environments without extensive testing & modification - you'll need to add production-ready validation, first.

## deployvcenter.ps1

This small script will deploy the VMware vCenter Appliance to a host in your Nutanix cluster.
To use, open the script and set your environment-specific settings.

## configurevcenter.ps1

This script is used to configure a vCenter Appliance with the recommended settings for use on Nutanix.
To use, open the script and set your environment-specific settings.  The settings in the script should be self-explanatory.

## api-demo

Very basic text-based demo of a script that uses the Nutanix API

## graphical-api-demo

More extensive version of the API demo but designed to be on a web server.  Shows cluster-specific information not contained in the text demo.

## api-demo-2.0

Laravel Lumen version of the graphical API demo.  This isn't recommended for normal use unless you understand how to use Packagist/Composer and the requirements to make them work.