######################################################
#                                                    #
# Environment-specific settings                      #
# Change these as necessary                          #
#                                                    #
# The default configuration below will work after    #
# Foundation if no changes have been made            #
#                                                    #
# You will almost definitely have to change the      #
# $viServer IP address ...                           #
#                                                    #
######################################################

# vCenter IP address and credentials
# Script default is vCenter default i.e. "root" and "vmware"

$viServer = "10.10.10.72"
$viUser = "root"
$viPassword = "vmware"

# ESX credentials

$esxUser = "root"
$esxPassword = "<esx password>"

# vSphere Datacenter and Cluster names

$dcName = "NTNX-DC"
$clusterName = "NTNX-Cluster"

# DNS server address

$dnsIP = "10.10.10.230"

# The NTP servers below will be *removed* from the VM Hosts

$ntpServersToRemove = @( "10.10.10.230", "0.north-america.pool.ntp.org", "1.north-america.pool.ntp.org", "2.north-america.pool.ntp.org", "3.north-america.pool.ntp.org" )

# The NTP servers below will be added to the VM hosts

$ntpServersToAdd = @("10.10.10.230")

# Default gateway and DNS domain name to apply to the hosts

$gwIP = "10.10.10.1"
$domain = "ntnxdemo.local"

######################################################
#                                                    #
# These percentages will work for a 3 host cluster   #
# They should be calculated properly to avoid        #
# your cluster's HA configuration being incorrect    #
#                                                    #
######################################################

$HACPUPercent = 33
$HAMemPercent = 33

# These advanced settings will all be set to true
# Note that das.ignoreRedundantNetWarning is set because many Nutanix SE blocks are connected with single 1GbE only during demos (e.g. mine)

$advancedSettings = @("das.ignoreRedundantNetWarning", "das.ignoreInsufficientHbDatastore")

# List of hosts to add to the new cluster

$vmHosts = @("10.10.10.20", "10.10.10.21", "10.10.10.22" )

######################################################
#                                                    #
# You shouldn't have to edit anything below here ... #
#                                                    #
######################################################

# Connect to the vCenter server

Connect-VIServer $viServer -Protocol https -User $viUser -Password $viPassword

# Create top-level folder and DC, if they doesn't already exist

$dc = Get-Datacenter -Name $dcName -ErrorAction SilentlyContinue
if( -Not $dc )
{
	New-Datacenter -Location (Get-Folder -NoRecursion | New-Folder -Name Nutanix) -Name $dcName
}

# Create Nutanix vSphere Cluster, if it doesn't already exist
# This doesn't configure DRS or HA, yet

$cluster = Get-Cluster $clusterName -ErrorAction SilentlyContinue
if( -Not $cluster )
{
	New-Cluster -Location (Get-Datacenter $dcName) -Name $clusterName
}

# Set Nutanix-specific vSphere Cluster configuration

Set-Cluster (Get-Cluster $clusterName) -DRSEnabled $true -DRSAutomationLevel PartiallyAutomated -HAAdmissionControlEnabled $true -HAEnabled $true -VMSwapFilePolicy WithVM -Confirm:$false

# Configure Admission Control percentages

$spec = New-Object VMware.Vim.ClusterConfigSpecEx
$spec.dasConfig = New-Object VMware.Vim.ClusterDasConfigInfo
$spec.dasConfig.admissionControlPolicy = New-Object VMware.Vim.ClusterFailoverResourcesAdmissionControlPolicy
$spec.dasConfig.admissionControlPolicy.cpuFailoverResourcesPercent = $HACPUPercent
$spec.dasConfig.admissionControlPolicy.memoryFailoverResourcesPercent = $HAMemPercent
$Cluster = Get-View (Get-Cluster -Name $clusterName)
$Cluster.ReconfigureComputeResource_Task( $spec, $true )

# Configure HA advanced settings
# Note that additional HA Advanced Settings can be specified above

foreach( $advancedSetting in $advancedSettings )
{
	$settingTest = Get-AdvancedSetting -Entity (Get-Cluster -Name $clusterName) -Name $advancedSetting
	if( -Not $settingTest )
	{
		New-AdvancedSetting -Entity (Get-Cluster -Name $clusterName) -Name $advancedSetting -Value true -Type ClusterHA -Confirm:$false
	}
}

# Add hosts to vSphere Cluster
# Omitting the -Force parameter will cause the process to fail due to invalid SSL host certificate on default installations

foreach( $vmHost in $vmHosts )
{
    # Check to see if the host is already in the cluster

	$hostTest = Get-VMHost -Location $dcName $vmHost -ErrorAction SilentlyContinue
	if( -Not $hostTest )
	{
        # Add hosts to the new cluster		

		Add-VMHost $vmHost -Location (Get-Cluster -Name $clusterName) -User $esxUser -Password $esxPassword -Force
		$vmHostNetworkInfo = Get-VMHostNetwork -Host $vmHost

        # Set the host network configuration

		Set-VMHostNetwork -Network $vmHostNetworkInfo -VMKernelGateway $gwIP -Domain $domain -DNSFromDHCP $false -DNSAddress $dnsIP

        # Remove all NTP servers specified in the array above
        # Ensures clean configuration

		foreach( $ntpServer in $ntpServersToRemove )
		{
			Remove-VMHostNtpServer -NtpServer $ntpServer -VMHost $vmHost -Confirm:$false -ErrorAction SilentlyContinue
		}

        # Add new NTP servers specified in array above

		foreach( $ntpServer in $ntpServersToAdd )
		{
			Add-VMHostNTPServer -NtpServer $ntpServer -VMHost $vmHost
		}

        # Check to see if Lockdown mode is enabled on the host
        # If it is, disable it
        # Note that check needs to happen first as disabling Lockdown mode command fails if Lockdown is already disabled

		if( ( Get-VMHost $vmHost | Get-View ).Config.AdminDisabled )
		{
			(Get-VMHost $vmHost | Get-View).ExitLockdownMode()
		}
	}
}

# Configure CVM-specific HA & DRA settings
# Note that this builds a list of VMs that have "CVM" in their name ...

$cvms = Get-VM | where { $_.Name -match "CVM" }

foreach( $cvm in $cvms )
{
	Set-VM $cvm.Name -HARestartPriority Disabled -HAIsolationResponse DoNothing -DRSAutomationLevel Disabled -Confirm:$false

    # Disable VM monitoring on the CVMs only (doesn't touch any other VMs)

    $spec = New-Object VMware.Vim.ClusterConfigSpecEx
    $spec.dasVmConfigSpec = New-Object VMware.Vim.ClusterDasVmConfigSpec
    $spec.dasVmConfigSpec[0].operation = "edit"
    $spec.dasVmConfigSpec[0].info = New-Object VMware.Vim.ClusterDasVmConfigInfo
    $spec.dasVmConfigSpec[0].info.key = New-Object VMware.Vim.ManagedObjectReference
    $spec.dasVmConfigSpec[0].info.key.value = $cvm.ExtensionData.MoRef.Value
    $spec.dasVmConfigSpec[0].info.dasSettings = New-Object VMware.Vim.ClusterDasVmSettings
    $spec.dasVmConfigSpec[0].info.dasSettings.vmToolsMonitoringSettings = New-Object VMware.Vim.ClusterVmToolsMonitoringSettings
    $spec.dasVmConfigSpec[0].info.dasSettings.vmToolsMonitoringSettings.enabled = $false
    $spec.dasVmConfigSpec[0].info.dasSettings.vmToolsMonitoringSettings.vmMonitoring = "vmMonitoringDisabled"
    $spec.dasVmConfigSpec[0].info.dasSettings.vmToolsMonitoringSettings.clusterSettings = $false
    $_this = Get-View -Id $cvm.VMHost.Parent.Id
    $_this.ReconfigureComputeResource_Task( $spec, $true )
}

# Disconnect from the vCenter server

Disconnect-VIServer -Confirm:$false