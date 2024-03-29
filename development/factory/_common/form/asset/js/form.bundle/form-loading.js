( function( $ ) {

    var _removeAdminPageFrameworkLoadingOutputs = function() {

        jQuery( '.admin-page-framework-form-loading' ).remove();
        jQuery( '.admin-page-framework-form-js-on' )
            .hide()
            .css( 'visibility', 'visible' )
            .fadeIn( 200 )
            .removeClass( '.admin-page-framework-form-js-on' );

    }

    /**
     * When some plugins or themes have JavaScript errors and the script execution gets stopped,
     * remove the style that shows "Loading...".
     */
    var _oneerror = window.onerror;
    window.onerror = function(){

        // We need to show the form.
        _removeAdminPageFrameworkLoadingOutputs();

        // Restore the original
        window.onerror = _oneerror;

        // If the original object is a function, execute it;
        // otherwise, discontinue the script execution and show the error message in the console.
        return "function" === typeof _oneerror
            ? _oneerror()
            : false;

    }

    /**
     * Rendering forms is heavy and unformatted layouts will be hidden with a script embedded in the head tag.
     * Now when the document is ready, restore that visibility state so that the form will appear.
     */
    jQuery( document ).ready( function() {
        _removeAdminPageFrameworkLoadingOutputs();
    });

    /**
     * Gets triggered when a widget of the framework is saved.
     * @since    3.7.0
     */
    $( document ).on( 'admin-page-framework_saved_widget', function( event, oWidget ){
        jQuery( '.admin-page-framework-form-loading' ).remove();
    });

}( jQuery ));