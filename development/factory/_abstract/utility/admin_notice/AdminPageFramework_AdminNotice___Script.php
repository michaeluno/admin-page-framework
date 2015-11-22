<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides a method to load the Javascript script to fade-in admin notices.
 * 
 * @since       DEVVER
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 * @extends     AdminPageFramework_WPUtility
 */
class AdminPageFramework_AdminNotice___Script extends AdminPageFramework_Factory___Script_Base {

    /**
     * The initial set-ups.
     */
    public function load() {
        wp_enqueue_script( 'jquery' );        
    }
    
    /**
     * Returns an inline JavaScript script.
     * 
     * @since       DEVVER
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {
        
        // Uncomment these lines when parameters need to be accessed.
        // $_aParams   = func_get_args() + array( null );
        // $_oMsg      = $_aParams[ 0 ];         
        
        /**
         * Checks checkboxes in siblings.
         */
        return <<<JAVASCRIPTS
( function( $ ) {
    jQuery( document ).ready( function() {         

        var _oAdminNotices = jQuery( '.admin-page-framework-settings-notice-message' );
        if ( _oAdminNotices.length ) {
                    
            // Animation of the `slideDown()` method does not work well when the target element has a margin
            // so enclose the elemnet in a new container and apply new margins to it.
            var _oContainer     = jQuery( _oAdminNotices )
                .css( 'margin', '0' )   // prevents jumpy animation
                .wrap( "<div class='admin-page-framework-admin-notice-animation-container'></div>" );
            _oContainer.css( 'margin-top', '1em' );
            _oContainer.css( 'margin-bottom', '1em' );
            
            // Now animate.
            jQuery( _oAdminNotices )
                .css( 'visibility', 'hidden' )
                .slideDown( 800 )
                .css( {opacity: 0, visibility: 'visible'})
                .animate( {opacity: 1}, 400 );
                
        }

    });              

}( jQuery ));
JAVASCRIPTS;
        
    }

}