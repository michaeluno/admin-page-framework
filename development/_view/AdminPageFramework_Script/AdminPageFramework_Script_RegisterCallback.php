<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for registering callbacks.
 * 
 * @since           3.0.0     
 * @since           3.3.0       Extends `AdminPageFramework_Script_Base`.
 * @package         AdminPageFramework
 * @subpackage      JavaScript
 * @internal
 */
class AdminPageFramework_Script_RegisterCallback extends AdminPageFramework_Script_Base {
    
    /**
     * Returns the script.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     */
    static public function getScript() {
        
        $_aParams   = func_get_args() + array( null );
        $_oMsg      = $_aParams[ 0 ];                
        
        return <<<JAVASCRIPTS
(function ( $ ) {
            
    // Callback containers.
    $.fn.aAPFAddRepeatableFieldCallbacks        = [];            
    $.fn.aAPFRemoveRepeatableFieldCallbacks     = [];
    $.fn.aAPFSortedFieldsCallbacks              = [];            
    $.fn.aAPFStoppedSortingFieldsCallbacks      = [];
    $.fn.aAPFAddedWidgetCallbacks               = [];
    
    /**
     * Gets triggered when a repeatable field add button is pressed.
     */
    $.fn.callBackAddRepeatableField = function( sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ) {
        var oThisNode = this;
        $.fn.aAPFAddRepeatableFieldCallbacks.forEach( function( hfCallback ) {
            if ( jQuery.isFunction( hfCallback ) ) { 
                hfCallback( oThisNode, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ); 
            }
        });
    };
    
    /**
     * Gets triggered when a repeatable field remove button is pressed.
     */
    $.fn.callBackRemoveRepeatableField = function( sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ) {
        var oThisNode = this;
        $.fn.aAPFRemoveRepeatableFieldCallbacks.forEach( function( hfCallback ) {
            if ( jQuery.isFunction( hfCallback ) ) { 
                hfCallback( oThisNode, sFieldType, sID, iCallType, iSectionIndex. iFieldIndex );
            }
        });
    };

    /**
     * Gets triggered when a sortable field is dropped and the sort event occurred
     */
    $.fn.callBackSortedFields = function( sFieldType, sID, iCallType ) {
        var oThisNode = this;
        $.fn.aAPFSortedFieldsCallbacks.forEach( function( hfCallback ) {
            if ( jQuery.isFunction( hfCallback ) ) { 
                hfCallback( oThisNode, sFieldType, sID, iCallType ); 
            }
        });
    };

    /**
     * Gets triggered when sorting fields stopped.
     * @since   3.1.6
     */
    $.fn.callBackStoppedSortingFields = function( sFieldType, sID, iCallType ) {
        var oThisNode = this;
        $.fn.aAPFStoppedSortingFieldsCallbacks.forEach( function( hfCallback ) {
            if ( jQuery.isFunction( hfCallback ) ) { 
                hfCallback( oThisNode, sFieldType, sID, iCallType ); 
            }
        });
    };            
    
    /**
     * Gets triggered when a widget of the framework is saved.
     * @since    3.2.0 
     */
    $( document ).bind( 'admin_page_framework_saved_widget', function( event, oWidget ){

        $.each( $.fn.aAPFAddedWidgetCallbacks, function( iIndex, hfCallback ) {
            
            if ( ! $.isFunction( hfCallback ) ) { return true; }   // continue

            hfCallback( oWidget ); 
            
        });            
    
    });            
    
    /**
     * Registers callbacks. This will be called in each field type definition class.
     */
    $.fn.registerAPFCallback = function( oOptions ) {
        
        // This is the easiest way to have default options.
        var oSettings = $.extend(
            {
                // The user specifies the settings with the following options.
                added_repeatable_field:     null,
                removed_repeatable_field:   null,
                sorted_fields:              null,
                stopped_sorting_fields:     null,
                saved_widget:               null,
            }, 
            oOptions 
        );

        // Store the callback functions
        $.fn.aAPFAddRepeatableFieldCallbacks.push( oSettings.added_repeatable_field );
        $.fn.aAPFRemoveRepeatableFieldCallbacks.push( oSettings.removed_repeatable_field );
        $.fn.aAPFSortedFieldsCallbacks.push( oSettings.sorted_fields );
        $.fn.aAPFStoppedSortingFieldsCallbacks.push( oSettings.stopped_sorting_fields );
        $.fn.aAPFAddedWidgetCallbacks.push( oSettings.saved_widget );
        
        return;

    };
    
}( jQuery ));
JAVASCRIPTS;
        
    }

}