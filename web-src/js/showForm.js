/**
 * Created by victor on 12.01.16.
 */

$( document ).ready(function() {

    $( "a.edit, #show_add_form" ).click(function(event) {
        event.preventDefault();
        $.ajax({
            url         : $(this).attr('href'),
            dataType    : "html",
            method      : "GET",
            statusCode  : {
                404: function() {
                    alert( "page not found" );
                }
            }
        }).done(function( html ) {
            $( "#formBlock" ).html( html ).slideDown();
        });
    });

});
