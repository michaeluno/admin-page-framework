<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_MediaUploader' ) ) :
/**
 * Provides JavaScript scripts to handle widget events.
 * 
 * @since       3.2.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 * @deprecated  Not implemented yet.
 */
class AdminPageFramework_Script_MediaUploader {

    static public function getjQueryPlugin() {
        
        // means the WordPress version is 3.4.x or below
        if ( ! function_exists( 'wp_enqueue_media' ) ) { return ""; } 
 
        
        /**
         * Returns the uploader frame object.
         * 
         * @since   3.2.0
         */     
        return "(function ( $ ) {
        }( jQuery ));";     
        
    }

}
endif;