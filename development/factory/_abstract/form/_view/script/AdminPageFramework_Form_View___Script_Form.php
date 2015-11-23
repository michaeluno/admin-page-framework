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
 * @since       DEVVER      
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
    
    /**
     * Renderisn forms is heavy and unformatted layouts will be hidden with a script embedded in the head tag.
     * Now when the document is ready, restore that visibility state so that the form will appear.
     */
    jQuery( document ).ready( function() {
        
        jQuery( '.admin-page-framework-form-loading' ).remove();
        jQuery( '.admin-page-framework-form-js-on' )
            .hide()
            .css( 'visibility', 'visible' )
            .fadeIn( 200 )
            .removeClass( '.admin-page-framework-form-js-on' )
            ;
            
    });    

}( jQuery ));
JAVASCRIPTS;
        
    }

    /**
     * Indicates whether the tab enabler script is loaded or not.
     */
    static private $_bLoadedTabEnablerScript = false;
    
}