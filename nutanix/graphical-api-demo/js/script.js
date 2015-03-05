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

            /* Submit he request via AJAX */
            request = $.ajax({
                url: "/ajax/do-the-demo.php",
                type: "post",
                dataType: "json",
                data: serializedData
            });

            request.success( function(data) {
                if( data.result == 'ok' )
                {
                    $( '#clusterError' ).slideUp( 300 );
                    $( 'span#cluster-id').html( data.id );
                    $( 'span#cluster-name').html( data.name );
                    $( 'span#cluster-timezone').html( data.timezone );
                    $( 'span#cluster-nodes').html( data.numNodes);
                    $( 'span#cluster-shadow-clones').html( data.enableShadowClones );

                    $( 'span#block-sn').html( data.blockZeroSn ),
                    $( 'span#cluster-ip').html( data.clusterIP ),
                    $( 'span#nos').html( data.nosVersion ),
                    $( 'span#hypervisors').html('');

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

                    $( 'span#sed').html( data.hasSED),
                    $( 'span#iops' ).html( data.numIOPS ),

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
