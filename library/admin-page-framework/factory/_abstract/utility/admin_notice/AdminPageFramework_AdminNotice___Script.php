<?php
class AdminPageFramework_AdminNotice___Script extends AdminPageFramework_Factory___Script_Base {
    public function load() {
        wp_enqueue_script('jquery');
    }
    static public function getScript() {
        return <<<JAVASCRIPTS
( function( $ ) {

    jQuery( document ).ready( function() {         

        var _oAdminNotices = jQuery( '.admin-page-framework-settings-notice-message' );
        if ( _oAdminNotices.length ) {
            jQuery( _oAdminNotices ).fadeIn( 'slow' );
        }

    });              


}( jQuery ));
JAVASCRIPTS;
        
    }
}