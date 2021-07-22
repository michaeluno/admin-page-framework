(function ( $ ) {

    /**
     * Checks all the checkboxes in siblings.
     */
    $.fn.selectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .prop( 'checked', true )
            .trigger( 'change' );   // 3.8.8+
    }
    /**
     * Unchecks all the checkboxes in siblings.
     */
    $.fn.deselectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .prop( 'checked', false )
            .trigger( 'change' );   // 3.8.8+
    }

}( jQuery ));