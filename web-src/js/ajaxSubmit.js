/**
 * Created by victor on 08.01.16.
 */

$( document ).ready(function() {
    $( "#formBlock" ).on( "submit", "form", function( event ) {

        event.preventDefault();

        var $form = $( this );
        var values = {};

        $.each( $form.serializeArray(), function(i, field) {
            values[field.name] = field.value;
        });

        $.ajax({
            type        : $form.attr( 'method' ),
            url         : $form.attr( 'action' ),
            data        : values,
            success     : function( html ) {
                $( "#formBlock" ).html( html );
                //window.location.href = '';
            }
        });
    });
});
