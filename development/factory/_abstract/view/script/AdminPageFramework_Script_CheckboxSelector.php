<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts to store temporary option data.
 * 
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_CheckboxSelector extends AdminPageFramework_Script_Base {

    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.3.0
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
(function ( $ ) {

    /**
     * Checks all the checkboxes in siblings.
     */        
    $.fn.selectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .attr( 'checked', true );                
    }
    /**
     * Unchecks all the checkboxes in siblings.
     */
    $.fn.deselectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .attr( 'checked', false );                             
    }          

}( jQuery ));
JAVASCRIPTS;
        
    }

}