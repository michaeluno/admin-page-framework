<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for forms.
 * 
 * @since       3.7.0      
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_Form extends AdminPageFramework_Form_View___Script_Base {
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.2.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */        
    static public function getScript( /* $oMsg */ ) {
        
        // Uncomment these lines when parameters need to be accessed.
        // $_aParams   = func_get_args() + array( null );
        // $_oMsg      = $_aParams[ 0 ];            
        
        return <<<JAVASCRIPTS
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
        
        // Discontinue the script execution and show the error message in the console.
        return false;
       
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
    $( document ).bind( 'admin_page_framework_saved_widget', function( event, oWidget ){
        jQuery( '.admin-page-framework-form-loading' ).remove();
    });    
    
}( jQuery ));
JAVASCRIPTS;
        
    }

    /**
     * Indicates whether the tab enabler script is loaded or not.
     */
    static private $_bLoadedTabEnablerScript = false;
    
}