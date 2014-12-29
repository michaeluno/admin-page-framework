<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript utility scripts.
 * 
 * @since       3.0.0     
 * @since       3.2.0       Extends `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_Utility extends AdminPageFramework_Script_Base {

    /**
     * Returns the script.
     * 
     * @since   3.0.0
     * @since   3.3.0   Changed the name from `getjQueryPlugin()`.
     */
    static public function getScript() {
        
        $_aParams   = func_get_args() + array( null );
        $_oMsg      = $_aParams[ 0 ];                   
        
        return <<<JAVASCRIPTS
( function( $ ) {
    $.fn.reverse = [].reverse;

    $.fn.formatPrintText = function() {
        var aArgs = arguments;     
        return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
            return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
                ? aArgs[ parseInt( number ) + 1 ]
                : match;
        });
    };
}( jQuery ));
JAVASCRIPTS;
        
    }

}