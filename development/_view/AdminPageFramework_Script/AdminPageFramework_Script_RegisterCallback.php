<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_RegisterCallback' ) ) :
/**
 * Provides JavaScript scripts for registering callbacks.
 * 
 * @since 3.0.0     
 * @package AdminPageFramework
 * @subpackage JavaScript
 * @internal
 */
class AdminPageFramework_Script_RegisterCallback {

    static public function getjQueryPlugin() {
        
        /**
         * 
         * @param string sFieldType     the field type
         * @param sID     the field container tag ID
         * @param iCallType     the function call type that indicates whether it's from repeatable sections or not.
         */
        return "(function ( $ ) {
                        
            // The method that gets triggered when a repeatable field add button is pressed.
            $.fn.callBackAddRepeatableField = function( sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ) {
                var oThisNode = this;
                if ( ! $.fn.aAPFAddRepeatableFieldCallbacks ) $.fn.aAPFAddRepeatableFieldCallbacks = [];
                $.fn.aAPFAddRepeatableFieldCallbacks.forEach( function( hfCallback ) {
                    if ( jQuery.isFunction( hfCallback ) ) { 
                        hfCallback( oThisNode, sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ); 
                    }
                });
            };
            
            // The method that gets triggered when a repeatable field remove button is pressed.
            $.fn.callBackRemoveRepeatableField = function( sFieldType, sID, iCallType, iSectionIndex, iFieldIndex ) {
                var oThisNode = this;
                if ( ! $.fn.aAPFRemoveRepeatableFieldCallbacks ) $.fn.aAPFRemoveRepeatableFieldCallbacks = [];
                $.fn.aAPFRemoveRepeatableFieldCallbacks.forEach( function( hfCallback ) {
                    if ( jQuery.isFunction( hfCallback ) ) { 
                        hfCallback( oThisNode, sFieldType, sID, iCallType, iSectionIndex. iFieldIndex );
                    }
                });
            };

            // The method that gets triggered when a sortable field is dropped and the sort event occurred
            $.fn.callBackSortedFields = function( sFieldType, sID, iCallType ) {
                var nodeThis = this;
                if ( ! $.fn.aAPFSortedFieldsCallbacks ) $.fn.aAPFSortedFieldsCallbacks = [];
                $.fn.aAPFSortedFieldsCallbacks.forEach( function( hfCallback ) {
                    if ( jQuery.isFunction( hfCallback ) ) { 
                        hfCallback( nodeThis, sFieldType, sID, iCallType ); 
                    }
                });
            };
            
            // The method that registers callbacks. This will be called in each field type definition class.
            $.fn.registerAPFCallback = function( oOptions ) {
                
                // This is the easiest way to have default options.
                var oSettings = $.extend({
                    // The user specifies the settings with the following options.
                    added_repeatable_field: function() {},
                    removed_repeatable_field: function() {},
                    sorted_fields: function() {},
                }, oOptions );

                // Set up arrays to store callback functions
                if( ! $.fn.aAPFAddRepeatableFieldCallbacks ) $.fn.aAPFAddRepeatableFieldCallbacks = [];
                if( ! $.fn.aAPFRemoveRepeatableFieldCallbacks ) $.fn.aAPFRemoveRepeatableFieldCallbacks = [];
                if( ! $.fn.aAPFSortedFieldsCallbacks ) $.fn.aAPFSortedFieldsCallbacks = [];

                // Store the callback functions
                $.fn.aAPFAddRepeatableFieldCallbacks.push( oSettings.added_repeatable_field );
                $.fn.aAPFRemoveRepeatableFieldCallbacks.push( oSettings.removed_repeatable_field );
                $.fn.aAPFSortedFieldsCallbacks.push( oSettings.sorted_fields );
                
                return;

            };
            
        }( jQuery ));";
        
    }

}
endif;