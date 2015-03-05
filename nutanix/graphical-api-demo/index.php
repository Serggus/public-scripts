<!doctype html>

<html lang="en-us">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/css/smoothness/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
    <title>Graphical Nutanix API Demo</title>
</head>

<body style="margin-top: 10px;">

<div class="col-md-12">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-demo"><i class="fa fa-desktop">&nbsp;</i>API Demo</a></li>
            <li><a href="#tabs-help"><i class="fa fa-question-circle">&nbsp;</i>Help</a></li>
        </ul>
        <div id="tabs-demo">
            <form id="config-form">
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
                        <input class="form-control" type="text" name="cluster-password" id="cluster-password" />
                    </div>
                    <div class='col-md-2'>
                        <label for="cluster-timeout">Timeout (Seconds)</label>
                        <input class="form-control" type="text" name="cluster-timeout" id="cluster-timeout" value="3" />
                    </div>
                </div>
                <div class="row" style="padding-top: 15px;">
                    <div class="col-md-2 col-md-offset-5">
                        <input type="submit" id="submit-go" class="form-control" value="Go!">
                    </div>
                </div>
            </form>
            <div class="row" style="margin-top: 15px;">
                <div class="col-md-8 col-md-offset-2">

                    <div id="clusterDetails" style="display: none;">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cluster Details</h3>
                            </div>
                            <div class="panel-body">
                                Cluster ID: <span id="cluster-id"></span><br>
                                Cluster Name: <span id="cluster-name"></span><br>
                                Cluster Time Zone: <span id="cluster-timezone"></span><br>
                                Cluster Nodes: <span id="cluster-nodes"></span><br>
                                First block's serial number: <span id="block-sn"></span><br>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cluster Configuration</h3>
                            </div>
                            <div class="panel-body">
                                Shadow Clones Enabled: <span id="cluster-shadow-clones"></span><br>
                                Cluster IP: <span id="cluster-ip"></span><br>
                                Nutanix OS version: <span id="nos"></span><br>
                                Hypervisor Types: <span id="hypervisors"></span><br>
                                Self-encrypting drives installed? <span id="sed"></span><br>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Stats</h3>
                            </div>
                            <div class="panel-body">
                                Current IOPS: <span id="iops"></span><br>
                            </div>
                        </div>
                    </div>

                    <div id="clusterError" class="panel panel-default ui-state-error" style="display: none;">
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
        <div id="tabs-help">
            <p class="h3">How do I use this demo?</p>
            <br>
            <p>Simple!&nbsp;&nbsp;Just plug your CVM details and credentials into the tab labelled &quot;API Demo&quot;, hit the Go! button and let the demo page do the rest.</p>
            <p class="h3">What if ... ?</p>
            <br>
            <p>
                ... I don't enter a cvm IP address?&nbsp;&nbsp;Sorry, this parameter is required.<br>
                ... I don't enter a cvm port?&nbsp;&nbsp;We'll attempt to connect on the default port - 9440.<br>
                ... I don't enter a cluster username?&nbsp;&nbsp;We'll attempt to connect with the default Prism username - 'admin'.<br>
                ... I don't enter a cluster password?&nbsp;&nbsp;We'll attempt to connect with the default Prism username - 'admin'.
            </p>
            <p class="h3">Who can I contact to ask questions?</p>
            <br>
            <p>Chris Rasmussen (<a href="mailto:crasmussen@nutanix.com">crasmussen@nutanix.com</a>), Melbourne SE, made this demo so that's a great place to start.  :)</p>
        </div>
    </div>
</div>

<div id="dialog_no_cvm_address" style="display: none;">
    <p class="h2">Awww!</p>
    <p>The CVM address is required for this demo to run.&nbsp;&nbsp;Please close this dialog, enter a valid CVM address then try again.</p>
</div>

<script src="/js/jquery-1.11.2.min.js"></script>
<script src="/css/smoothness/jquery-ui.min.js"></script>
<script src="/js/script.js"></script>

<script>

</script>

</body>

</html>