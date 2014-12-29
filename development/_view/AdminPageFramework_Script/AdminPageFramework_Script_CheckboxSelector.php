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
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_CheckboxSelector extends AdminPageFramework_Script_Base {

    /**
     * Returns the JavaScript script.
     * 
     * @since       3.3.0
     */
    static public function getScript() {
        
        $_aParams   = func_get_args() + array( null );
        $_oMsg       = $_aParams[ 0 ];                
            
        /**
         * Checks checkboxes in siblings.
         */
        return <<<JAVASCRIPTS
(function ( $ ) {

    /**
     * Checks all the checkboxes in siblings.
     */        
    $.fn.selectALLAPFCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .attr( 'checked', true );                
    }
    /**
     * Unchecks all the checkboxes in siblings.
     */
    $.fn.deselectAllAPFCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .attr( 'checked', false );                             
    }          

}( jQuery ));
JAVASCRIPTS;
        
    }

}