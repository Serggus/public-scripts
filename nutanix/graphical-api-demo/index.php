<!doctype html>

<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/master.css">
    <title>Graphical Nutanix API Demo</title>
</head>

<body style="margin-top: 10px;">

<form id="config-form">

<div class="col-md-12">

    <div class="panel panel-default">
        <div class="panel-heading"><strong>Credentials</strong></div>
        <div class="panel-body">
                <div class='row'>
                    <div class='col-md-2'>
                        <label for="cvm-address">CVM Address</label>
                        <input class="form-control" type="text" name="cvm-address" id="cvm-address" />
                    </div>
                    <div class='col-md-2'>
                        <label for="cvm-port">CVM Port</label>
                        <input class="form-control" type="text" name="cvm-port" id="cvm-port" value="9440" />
                    </div>
                    <div class='col-md-2'>
                        <label for="cluster-username">Cluster Username</label>
                        <input class="form-control" type="text" name="cluster-username" id="cluster-username" />
                    </div>
                    <div class='col-md-2'>
                        <label for="cluster-password">Cluster Password</label>
                        <input class="form-control" type="password" name="cluster-password" id="cluster-password" />
                    </div>
                    <div class='col-md-2'>
                        <label for="cluster-timeout">Timeout (Seconds)</label>
                        <input class="form-control" type="text" name="cluster-timeout" id="cluster-timeout" value="3" />
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            The credentials above will be used for all demos available below.&nbsp;&nbsp;When using the &quot;write&quot; demos, please make sure your credentials have the appropriate access to the selected CVM/cluster.
        </div>
    </div>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-read"><i class="fa fa-desktop">&nbsp;</i>API Demo - GET Request (Read)</a></li>
            <li><a href="#tabs-write"><i class="fa fa-desktop">&nbsp;</i>API Demo - POST Request (Write)</a></li>
            <li><a href="#tabs-msp" style="font-weight: bold;"><i class="fa fa-desktop">&nbsp;</i>API Demo - Self Service (Write)</a></li>
            <li><a href="#tabs-help"><i class="fa fa-question-circle">&nbsp;</i>Help</a></li>
        </ul>
        <div id="tabs-read">
            <p>This part of the demo shows how we can leverage the Nutanix API to get cluster information.</p>
            <p>For now, we'll collect some basic info and show it in the panels below.</p>
            <form id="config-form">
                <div class="row" style="padding-top: 15px;">
                    <div class="col-md-2 col-md-offset-5">
                        <input type="submit" id="submit-go" class="form-control" value="Go!">
                    </div>
                </div>
            </form>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-8 col-md-offset-2">

                    <div id="clusterDetails" class="none">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cluster Details [ <span id="cluster-name"></span> | <span id="cluster-id"></span> ]</h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4 item-header">NOS Version</div>
                                    <div class="col-md-4 item-header">Nodes</div>
                                    <div class="col-md-4 item-header">Hypervisor(s)</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 item-content"><span id="cluster-nosVersion"></span></div>
                                    <div class="col-md-4 item-content"><span id="cluster-numNodes"></span></div>
                                    <div class="col-md-4 item-content"><span id="hypervisors"></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cluster Storage</h3>
                            </div>
                            <div class="panel-body">
                                <span id="ssd_graph"></span>
                                <span id="hdd_graph"></span>
                            </div>
                            <table class="table" id="containers">
                                <tr>
                                    <td>Name</td>
                                    <td>RF</td>
                                    <td>Compression</td>
                                    <td>Comp. Delay (Minutes)</td>
                                    <td>Space Saved</td>
                                    <td>RAM/SSD Dedupe?</td>
                                    <td>HDD Dedupe?</td>
                                </tr>
                            </table>
                        </div>



                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cluster Configuration [ <span id="cluster-IP"></span> ]</h3>
                            </div>
                            <div class="panel-body">
                                Shadow Clones Enabled: <span id="cluster-enableShadowClones"></span><br>
                                Self-encrypting drives installed? <span id="cluster-hasSED"></span><br>
                            </div>
                        </div>

                    </div>

                    <div id="clusterError" class="panel panel-default ui-state-error none">
                        <div class="panel-heading">
                            <h3 class="panel-title">Cluster Error</h3>
                        </div>
                        <div class="panel-body">
                            <p>An error has occurred while processing this request.</p>
                            <p>Error details are as follows:</p>
                            <p><span id="cluster-error"></span></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="tabs-write">
            <p>This part of the demo shows how we can leverage the Nutanix API to make cluster changes.&nbsp;&nbsp;Please use caution when making cluster changes via scripts or custom applications, especially when running against production clusters.</p>
            <p>For now, we'll create a new simple container in the first Storage Pool the cluster finds.</p>
            <br>
            <div class='row'>
                <div class='col-md-10'>
                    <label for="container-name">Container Name</label>
                    <input class="form-control" type="text" name="container-name" id="container-name" />
                </div>
            </div>
            <div class="row" style="padding-top: 15px;">
                <div class="col-md-2 col-md-offset-5">
                    <input type="submit" id="submit-container" class="form-control" value="Create Container">
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-8 col-md-offset-2">

                    <div id="clusterDetails-container" class="none">
                        <div class="panel panel-success" style="text-align: center; padding: 10px 0; margin: 20px 0 0 0; background: #D6E9E0;">Container created successfully!&nbsp;&nbsp;:)</div>
                    </div>
                    <div id="clusterError-container" class="none">
                        <div class="panel panel-danger" id="container-error-message" style="text-align: center; padding: 10px 0; margin: 20px 0 0 0; background: #FF90A2;">Container creation failed.&nbsp;&nbsp;:(</div>
                    </div>

                </div>
            </div>
        </div>

        <div id="tabs-msp">
            <p>This is where leveraging a published API can get pretty cool.&nbsp;&nbsp;Using the fields below we are now able to implement a 'single click' process that will automate the creation of a VM that matches a particular specification.&nbsp;&nbsp;Please use caution when making cluster changes via scripts or custom applications, especially when running against production clusters.</p>
            <p>In these examples the server specifications are fairly arbitrary but could be easily modified to suit a real self-service application.</p>
            <div class="row" style="padding-top: 15px;">
                <div class="col-md-4">
                    <label for="server-profile">Select Server Profile:</label>
                    <select name="server-profile" id="server-profile" class="form-control">
                        <option selected id="profile-exchange" value="exch">Microsoft Exchange 2013 Mailbox</option>
                        <option id="profile-dc" value="dc">Domain Controller</option>
                        <option id="profile-web" value="lamp">Web Server (LAMP)</option>
                    </select>
                    <div class="get-profile" style="margin-top: 3px; font-size: 75%;">
                        <div id="profile-exchange-spec">&raquo;&nbsp;Microsoft Exchange specs: 2x CPU, 8GB RAM, 1x 120GB SCSI disk, 1x 500GB SCSI disk</div>
                        <div id="profile-dc-spec" style="display: none;">&raquo;&nbsp;Domain Controller specs: 1x CPU, 2GB RAM, 1x 250GB SCSI disk</div>
                        <div id="profile-web-spec" style="display: none;">&raquo;&nbsp;Web Server specs: 2x CPU, 4GB RAM, 1x 40GB disk</div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-4">
                    <label for="server-name">Enter Server Name:</label>
                    <input class="form-control" type="text" name="server-name" id="server-name" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-4" style="margin-top: 10px;">
                    <input type="submit" id="submit-msp" class="form-control" value="Create VM">
                </div>
            </div>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-8 col-md-offset-2">

                    <div id="clusterDetails-msp" class="none">
                        <div class="panel panel-success" style="text-align: center; padding: 10px 0; margin: 20px 0 0 0; background: #D6E9E0;">VM creation request submitted successfully!&nbsp;&nbsp;Why not go and have a look at Prism, now?&nbsp;&nbsp;:)</div>
                    </div>
                    <div id="clusterError-msp" class="none">
                        <div class="panel panel-danger" id="vm-error-message" style="text-align: center; padding: 10px 0; margin: 20px 0 0 0; background: #FF90A2;">Task request could not be submitted at this time.&nbsp;&nbsp;:(</div>
                    </div>

                </div>
            </div>
        </div>

        <div id="tabs-help">
            <p class="h3">How do I use this demo?</p>
            <br>
            <p>Simple!&nbsp;&nbsp;Just plug your CVM or cluster IP address details, credentials into the form at the top of the screen, select the tab for what you want to demo, enter the required info &amp; hit the appropriate submit button and let the demo page do the rest.</p>
            <p class="h3">What do I need?</p>
            <br>
            <p>
                &raquo;&nbsp;PHP >= 5.4 (you've probably already got it if you're reading this page)<br>
                &raquo;&nbsp;PHP with cURL capability - MAMP is recommended if you don't want to mess with manual configuration<br>
                &raquo;&nbsp;A connection to a Nutanix cluster running NOS >= 4.1 (stats objects were different before 4.1)<br>
                &raquo;&nbsp;Credentials for the relevant Nutanix cluster (read-only is fine, unless you are going to create a container)<br>
                &raquo;&nbsp;A recent web browser
            </p>
            <p class="h3">What if ... ?</p>
            <br>
            <p>
                ... I don't enter a cvm or cluster IP address?&nbsp;&nbsp;Sorry, this parameter is required.<br>
                ... I don't enter a container name?&nbsp;&nbsp;Sorry, this parameter is required, if you are creating a container.<br>
                ... I don't enter a VM name?&nbsp;&nbsp;Sorry, this parameter is required, if you are creating a VM.<br>
                ... I don't enter a cvm or cluster port?&nbsp;&nbsp;We'll attempt to connect on the default port - 9440.<br>
                ... I don't enter a cluster username or password?&nbsp;&nbsp;Sorry - authentication is required to use these demos.
            </p>
            <p class="h3">Who can I contact to ask questions?</p>
            <br>
            <p>Chris Rasmussen (<a href="mailto:crasmussen@nutanix.com">crasmussen@nutanix.com</a>), Melbourne SE, made this demo so that's a great place to start.  :)</p>
        </div>
    </div>
</div>

</form>

<div id="dialog_no_cvm_address" class="none">
    <p class="h2">Awww!</p>
    <p>The CVM address is required for this demo to run.&nbsp;&nbsp;Please close this dialog, enter a valid CVM address then try again.</p>
</div>

<div id="dialog_container_details" class="none">
    <p class="h2">Awww!</p>
    <p>Unfortunately some required information hasn't been provided.&nbsp;&nbsp;Please close this dialog and ensure you have entered both a valid CVM address &amp; container name, then try again.</p>
</div>

<div id="dialog-confirm" title="Continue?" style="display: none;">
    <p><br><i class="fa fa-2x fa-question-circle"></i>&nbsp;Even though this is a demo application, you are about to make <strong>REAL</strong> changes to the cluster.&nbsp;&nbsp;Are you sure you want to do that?</p>
</div>

<script src="js/jquery-1.11.2.min.js"></script>
<script src="css/smoothness/jquery-ui.min.js"></script>
<script src="js/script.js"></script>

<script>

</script>

</body>

</html>