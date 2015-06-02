function cvmAddressEntered( inputElement )
{
    if( $( inputElement ).val() == '' )
    {
        return false;
    }
    else
    {
        return true;
    }
}

function showErrorDialog( dialogElement )
{
    $( dialogElement ).dialog({
        resizable: false,
        height:220,
        width: 420,
        modal: true
    })
}

function getSerializedForm( formElement )
{
    var form = $( formElement );
    return( form.serialize() );
}

$(document).ready(function(){

    /* Convert the HTML nav element to jQuery UI tabs */
    $( '#tabs' ).tabs();

    /* Convert the configuration form's submit button to a jQuery UI button ... perrrrrrty  :) */
    $( 'input[type=submit]' ).button();

    $( '#server-profile').on( 'change', function(e) {
        switch( $( '#server-profile' ).val() )
        {
            case 'exch':
                $( 'div#profile-exchange-spec').slideDown( 300 );
                $( 'div#profile-dc-spec').slideUp( 300 );
                $( 'div#profile-web-spec').slideUp( 300 );
                break;
            case 'dc':
                $( 'div#profile-exchange-spec').slideUp( 300 );
                $( 'div#profile-dc-spec').slideDown( 300 );
                $( 'div#profile-web-spec').slideUp( 300 );
                break;
            case 'lamp':
                $( 'div#profile-exchange-spec').slideUp( 300 );
                $( 'div#profile-dc-spec').slideUp( 300 );
                $( 'div#profile-web-spec').slideDown( 300 );
                break;
        }
        e.preventDefault();
    });

    $( 'input#submit-msp').on( 'click', function(e) {

        $(function() {
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height: 240,
                width: 420,
                modal: true,
                buttons: {
                    "Yes, do it!": function() {
                        $( this ).dialog( "close" );
                        if( cvmAddressEntered( '#cvm-address' ) && ( $( '#server-name').val() != '' ) )
                        {
                            /* A CVM address and VM name were entered - we can carry on */

                            /* Submit the request via AJAX */
                            request = $.ajax({
                                url: "../ajax/ajaxCreateVM.php",
                                type: "post",
                                dataType: "json",
                                data: getSerializedForm( '#config-form' )
                            });

                            request.success( function(data) {
                                if( data.result == 'ok' )
                                {
                                    $( '#clusterError-msp' ).slideUp( 300 );
                                    $( '#clusterDetails-msp' ).slideDown( 300 );

                                }
                                else
                                {
                                    $( '#clusterDetails-msp' ).slideUp( 300 );
                                    $( '#vm-error-message' ).html( data.message);
                                    $( '#clusterError-msp' ).slideDown( 300 );
                                }
                            });

                            request.done(function (response, textStatus, jqXHR){
                                /* nothing here, yet ... maybe later */
                            });

                            request.fail(function ( jqXHR, textStatus, errorThrown )
                            {
                                /* Display an error message */
                                alert( 'Unfortunately an error occurred while processing the request.  Status: ' + textStatus + ', Error Thrown: ' + errorThrown );
                            });

                        }
                        else
                        {
                            /* CVM address or container name are missing */
                            showErrorDialog( "#dialog_container_details" );
                        }
                    },
                    "Nope, I'm bailing out!": function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });

        e.preventDefault();
    });

    $( 'input#submit-container').on( 'click', function(e) {

        $(function() {
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height: 240,
                width: 420,
                modal: true,
                buttons: {
                    "Yes, do it!": function() {
                        $( this ).dialog( "close" );
                        if( cvmAddressEntered( '#cvm-address' ) && ( $( '#container-name').val() != '' ) )
                        {
                            /* A CVM address and container name were entered - we can carry on */

                            console.log( getSerializedForm( '#config-form' ) );

                            /* Submit the request via AJAX */
                            request = $.ajax({
                                url: "../ajax/ajaxCreateContainer.php",
                                type: "post",
                                dataType: "json",
                                data: getSerializedForm( '#config-form' )
                            });

                            request.success( function(data) {
                                if( data.result == 'ok' )
                                {
                                    $( '#clusterError-container' ).slideUp( 300 );
                                    $( '#clusterDetails-container' ).slideDown( 300 );

                                }
                                else
                                {
                                    $( '#clusterDetails-container' ).slideUp( 300 );
                                    $( '#container-error-message' ).html( data.message);
                                    $( '#clusterError-container' ).slideDown( 300 );
                                }
                            });

                            request.done(function (response, textStatus, jqXHR){
                                /* nothing here, yet ... maybe later */
                            });

                            request.fail(function ( jqXHR, textStatus, errorThrown )
                            {
                                /* Display an error message */
                                alert( 'Unfortunately an error occurred while processing the request.  Status: ' + textStatus + ', Error Thrown: ' + errorThrown );
                            });

                        }
                        else
                        {
                            /* CVM address or container name are missing */
                            showErrorDialog( "#dialog_container_details" );
                        }
                    },
                    "Nope, I'm bailing out!": function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
        });

        e.preventDefault();
    });

    /* Sort out what to do when the form's submit button is clicked ... AJAX ftw */
    $( 'input#submit-go').on( 'click', function(e) {

        /*
            Make sure a CVM address was entered
            Everything else can be guessed based on Nutanix defaults
         */
        if( !cvmAddressEntered( '#cvm-address' ) )
        {
            /* No CVM address was entered */
            showErrorDialog( "#dialog_no_cvm_address" );
        }
        else
        {
            /* A CVM address was entered - we can carry on */

            /* Submit the request via AJAX */
            request = $.ajax({
                url: "../ajax/ajaxReadCluster.php",
                type: "post",
                dataType: "json",
                data: getSerializedForm( '#config-form' )
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
                        sed: { id: 'cluster-hasSED' }
                    };

                    $.each( dataFields, function( key, field ) {
                        $( 'span#' + field.id ).html( data[ field.id ] );
                    });

                    $( 'span#ssd_graph').html( '<img src="../ajax/charts/' + data.ssdGraph + '">' );
                    $( 'span#hdd_graph').html( '<img src="../ajax/charts/' + data.hddGraph + '">' );

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
                alert( 'Unfortunately an error occurred while processing the request.  Status: ' + textStatus + ', Error Thrown: ' + errorThrown );
            });

        }

        /* Prevent the form from being submitted normally */
        e.preventDefault();

    });

});
