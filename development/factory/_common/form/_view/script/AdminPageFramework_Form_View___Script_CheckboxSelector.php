<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides JavaScript scripts to store temporary option data.
 *
 * @since       3.3.0
 * @package     AdminPageFramework/Common/Form/View/JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_CheckboxSelector extends AdminPageFramework_Form_View___Script_Base {

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
            .prop( 'checked', true )
            .trigger( 'change' );   // 3.8.8+
    }
    /**
     * Unchecks all the checkboxes in siblings.
     */
    $.fn.deselectAllAdminPageFrameworkCheckboxes = function() {
        jQuery( this ).parent()
            .find( 'input[type=checkbox]' )
            .prop( 'checked', false )
            .trigger( 'change' );   // 3.8.8+
    }          

}( jQuery ));
JAVASCRIPTS;

    }

}
