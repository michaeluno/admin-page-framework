<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_CheckboxSelector' ) ) :
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
    static public function getScript( $oMsg=null ) {
        
        /**
         * Checks checkboxes in siblings.
         */     
        return "(function ( $ ) {
        
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
  
        }( jQuery ));";     
        
    }

}
endif;