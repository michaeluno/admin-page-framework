<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts to store temporary option data.
 * 
 * @since       3.1.6
 * @sicne       3.3.0       Extends `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_OptionStorage extends  AdminPageFramework_Script_Base {
    
    /**
     * Returns the script.
     * 
     * @since   3.1.6
     * @since   3.3.0   Changed the name from `getjQueryPlugin()`;
     */
    static public function getScript() {

        $_aParams   = func_get_args() + array( null );
        $_oMsg      = $_aParams[ 0 ];            
        
        /**
         * Stores framework JavaScript script options.
         */     
        return <<<JAVASCRIPTS
(function ( $ ) {
            
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

}( jQuery ));
JAVASCRIPTS;

    }

}