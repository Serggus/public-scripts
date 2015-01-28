######################################################
#                                                    #
# Environment-specific settings                      #
# Change these as necessary                          #
#                                                    #
######################################################

# Host IP address i.e. which host to deploy the vApp to

$esxHost = "10.10.10.20"

# ESX credentials

$esxUser = "root"
$esxPassword = "nutanix/4u"

# Datastore details i.e. which Datastore on the above Host to use

$datastoreName = "NTNX-Container"

# The vApp details

$vAppFilename = "C:\Shared\VMware-vCenter-Server-Appliance-5.5.0.20200-2183109_OVF10.ova"
$vAppName = "vCenter Server Virtual Appliance"

######################################################
#                                                    #
# You shouldn't have to edit anything below here ... #
#                                                    #
######################################################

# Connect to the Host

Connect-VIServer $esxHost -Protocol https -User $esxUser -Password $esxPassword

# Get the Host and Datastore details then deploy the vApp

$vmHost = Get-VMHost -Name $esxHost
$dataStore = Get-Datastore -Name "$datastoreName"

Import-VApp -Source $vAppFilename -VMHost $esxHost -Datastore $dataStore -Name "$vAppName"

# Disconnect from the Host

Disconnect-VIServer -Confirm:$false