(function($) {

    // Initialize
    $( document ).ready( function() {
        $( '.admin-page-framework-fields.sortable' ).each( function() {
            $( this ).enableAdminPageFrameworkSortableFields();
        } );
    });

    $.fn.enableAdminPageFrameworkSortableFields = function( sFieldsContainerID ) {

        var _oTarget    = 'string' === typeof sFieldsContainerID
            ? $( '#' + sFieldsContainerID + '.sortable' )
            : $( this );

        _oTarget.off( 'sortupdate' );
        _oTarget.off( 'sortstop' );
        var _oSortable  = _oTarget.sortable(
            // the options for the sortable plugin
            {
                items: '> div:not( .disabled )',
            }
        );

        // Callback the registered functions.
        _oSortable.on( 'sortstop', function() {
            $( this ).callBackStoppedSortingFields(
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );
        });
        _oSortable.on( 'sortupdate', function() {
            $( this ).callBackSortedFields(
                $( this ).data( 'type' ),
                $( this ).attr( 'id' ),
                0  // call type 0: fields, 1: sections
            );
        });

    };
}( jQuery ));