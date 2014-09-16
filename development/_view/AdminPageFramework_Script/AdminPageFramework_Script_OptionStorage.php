<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_OptionStorage' ) ) :
/**
 * Provides JavaScript scripts to store temporary option data.
 * 
 * @since       3.1.6
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_OptionStorage {

    static public function getjQueryPlugin() {
        
        /**
         * Stores framework JavaScript script options.
         */     
        return "(function ( $ ) {
            
            $.fn.aAPFInputOptions = {}; 
                                    
            $.fn.storeAPFInputOptions = function( sID, vOptions ) {
                var sID = sID.replace( /__\d+_/, '___' );	// remove the section index. The g modifier is not used so it will replace only the first occurrence.
                $.fn.aAPFInputOptions[ sID ] = vOptions;
            };	
            $.fn.getAPFInputOptions = function( sID ) {
                var sID = sID.replace( /__\d+_/, '___' ); // remove the section index
                return ( 'undefined' === typeof $.fn.aAPFInputOptions[ sID ] )
                    ? null
                    : $.fn.aAPFInputOptions[ sID ];
            }
  
        }( jQuery ));";     
        
    }

}
endif;