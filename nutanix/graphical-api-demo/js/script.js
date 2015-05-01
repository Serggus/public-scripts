$(document).ready(function(){

    /* Convert the HTML nav element to jQuery UI tabs */
    $( '#tabs' ).tabs();

    /* Convert the configuration form's submit button to a jQuery UI button ... perrrrrrty  :) */
    $( 'input[type=submit]' ).button();

    /* Sort out what to do when the form's submit button is clicked ... AJAX ftw */
    $( 'input#submit-go').on( 'click', function(e) {

        /*
            Make sure a CVM address was entered
            Everything else can be guessed based on Nutanix defaults
         */
        if( $( '#cvm-address' ).val() == '' )
        {
            /* No CVM address was entered */
            $( "#dialog_no_cvm_address" ).dialog({
                resizable: false,
                height:220,
                width: 420,
                modal: true
            })
        }
        else
        {
            /* A CVM address was entered - we can carry on */

            var form = $( '#config-form' );
            var serializedData = form.serialize();

            /* Submit the request via AJAX */
            request = $.ajax({
                url: "/ajax/ajaxAPI.php",
                type: "post",
                dataType: "json",
                data: serializedData
            });

            request.success( function(data) {
                if( data.result == 'ok' )
                {

                    $( '#clusterError' ).slideUp( 300 );

                    var dataFields = {
                        id: { id: 'cluster-id' },
                        name: { id: 'cluster-name' },
                        timezone: { id: 'cluster-timezone' },
                        nodes: { id: 'cluster-numNodes' },
                        sc: { id: 'cluster-enableShadowClones' },
                        sn: { id: 'cluster-blockZeroSn' },
                        ip: { id: 'cluster-IP' },
                        nos: { id: 'cluster-nosVersion' },
                        sed: { id: 'cluster-hasSED' },
                        iops: { id: 'cluster-numIOPS' }
                    };

                    $.each( dataFields, function( key, field ) {
                        $( 'span#' + field.id ).html( data[ field.id ] );
                    });

                    $( 'span#ssd_graph').html( '<img src="/ajax/charts/' + data.ssdGraph + '">' );
                    $( 'span#hdd_graph').html( '<img src="/ajax/charts/' + data.hddGraph + '">' );

                    $( 'span#hypervisors').html( '' );
                    $( data.hypervisorTypes ).each( function( index, item ) {
                        switch( item )
                        {
                            case 'kKvm':
                                $( 'span#hypervisors').append( 'KVM ' );
                                break;
                            case 'kVMware':
                                $( 'span#hypervisors').append( 'ESXi ' );
                                break;
                            case 'kHyperv':
                                $( 'span#hypervisors').append( 'Hyper-V ' );
                                break;
                        }
                    });

                    $( 'table#containers' ).html( '' ).html( '<tr><td>Name</td><td>RF</td><td>Compression</td><td>Comp. Delay (Minutes)</td><td>Space Saved</td><td>RAM/SSD Dedupe?</td><td>HDD Dedupe?</td></tr>' );
                    $( data.containers ).each( function( index, item ) {
                        $( 'table#containers tr:first' ).after(
                            '<tr><td>'+ item.name + '</td><td>' + item.replicationFactor + '</td>'
                            + ( item.compressionEnabled ? ( '<td>Yes</td><td>' + ( item.compressionDelay / 60 ) + '</td><td>' + item.compressionSpaceSaved + '</td>' ) : ( '<td>No</td><td>-</td><td>-</td>' ) )
                            + '<td>' + ( item.fingerprintOnWrite ? 'Yes' : 'No' ) + '</td>'
                            + '<td>' + ( item.onDiskDedup ? 'Yes' : 'No' ) + '</td>'
                            + '</tr>'
                        );
                    });

                    $( '#clusterDetails' ).slideDown( 300 );
                }
                else
                {
                    $( '#clusterDetails' ).slideUp( 300 );
                    $( 'span#cluster-error').html( data.message);
                    $( '#clusterError' ).slideDown( 300 );
                }
            });

            request.done(function (response, textStatus, jqXHR){
                /* nothing here, yet ... maybe later */
            });

            request.fail(function ( jqXHR, textStatus, errorThrown )
            {
                /* Display an error message */
                alert( 'Unfortunately an error occurred while process the request.  Status: ' + textStatus + ', Error Thrown: ' + errorThrown );
            });

        }

        /* Prevent the form from being submitted normally */
        e.preventDefault();

    });

});
