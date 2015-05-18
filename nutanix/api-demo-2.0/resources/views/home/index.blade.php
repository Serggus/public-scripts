@extends( 'layouts.master' )

@section( 'content' )

<div class="col-md-12">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-demo"><i class="fa fa-desktop">&nbsp;</i>API Demo</a></li>
            <li><a href="#tabs-help"><i class="fa fa-question-circle">&nbsp;</i>Help</a></li>
            <li><a href="#tabs-check"><i class="fa fa-exclamation-circle">&nbsp;</i>Will this work?</a></li>
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
                        <input class="form-control" type="password" name="cluster-password" id="cluster-password" />
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
        <div id="tabs-help">
            <p class="h3">How do I use this demo?</p>
            <br>
            <p>Simple!&nbsp;&nbsp;Just plug your CVM or cluster IP address details and credentials into the tab labelled &quot;API Demo&quot;, hit the Go! button and let the demo page do the rest.</p>
            <p class="h3">What do I need?</p>
            <br>
            <p>
                &raquo;&nbsp;A workstation or laptop configured as a development environment (<strong>php5-curl is required for cluster/CVM connectivity</strong>)<br>
                &raquo;&nbsp;PHP >= 5.4 (you've probably already got it if you're reading this page)<br>
                &raquo;&nbsp;A connection to a Nutanix cluster running NOS >= 4.1 (stats objects were different before 4.1)<br>
                &raquo;&nbsp;Credentials for the relevant Nutanix cluster (read-only is fine)<br>
                &raquo;&nbsp;A recent web browser
            </p>
            <p class="h3">Can I run the demo?</p>
            <br>
            <p>We've tried to check your computer - please see the 'Will this work?' tab for the results.</p>
            <p class="h3">What if ... ?</p>
            <br>
            <p>
                ... I don't enter a cvm or cluster IP address?&nbsp;&nbsp;Sorry, this parameter is required.<br>
                ... I don't enter a cvm or cluster port?&nbsp;&nbsp;We'll attempt to connect on the default port - 9440.<br>
                ... I don't enter a cluster username?&nbsp;&nbsp;We'll attempt to connect with the default Prism username - 'admin'.<br>
                ... I don't enter a cluster password?&nbsp;&nbsp;We'll attempt to connect with the default Prism username - 'admin'.
            </p>
            <p class="h3">Who can I contact to ask questions?</p>
            <br>
            <p>Chris Rasmussen (<a href="mailto:crasmussen@nutanix.com">crasmussen@nutanix.com</a>), Melbourne SE, made this demo so that's a great place to start.  :)</p>
        </div>
        <div id="tabs-check">
            <p class="h3">Will this demo work on my computer?</p>
            <br>
            <p>We've done a few checks to see if this demo <em>should</em> work on your computer ... how nice.&nbsp;&nbsp;:)&nbsp;&nbsp;Still, please treat these checks with caution as they aren't 100% reliable on all systems.</p>
            <br>
            <table class="table">
                <tr>
                    <td>&nbsp;</td>
                    <td>Check</td>
                    <td>Requirement</td>
                    <td>Result</td>
                </tr>
                <tr>
                    <td><?php echo( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ? '<i class="fa fa-thumbs-o-up" style="color: green;"></i>' : '<i class="fa fa-thumbs-o-down" style="color: red;"></i>' ); ?></td>
                    <td>PHP version</td>
                    <td>&gt;=5.4.0</td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td><?php echo( function_exists( 'curl_version' ) === true ? '<i class="fa fa-thumbs-o-up" style="color: green;"></i>'  : '<i class="fa fa-thumbs-o-down" style="color: red;"></i>' ); ?></td>
                    <td>PHP cURL enabled</td>
                    <td><code>curl_version</code> function available</td>
                    <td><?php echo( function_exists( 'curl_version' ) === true ? 'Yes' : 'No' ); ?></td>
                </tr>
            </table>

        </div>
    </div>
</div>

@stop

@include( 'partials.footer' )