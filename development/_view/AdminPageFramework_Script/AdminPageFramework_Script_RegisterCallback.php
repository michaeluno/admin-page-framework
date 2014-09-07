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
        
        return "(function ( $ ) {
            
            // Callback containers.
            $.fn.aAPFAddRepeatableFieldCallbacks        = [];            
            $.fn.aAPFRemoveRepeatableFieldCallbacks     = [];
            $.fn.aAPFSortedFieldsCallbacks              = [];            
            $.fn.aAPFStoppedSortingFieldsCallbacks      = [];
            
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
             * Registers callbacks. This will be called in each field type definition class.
             */
            $.fn.registerAPFCallback = function( oOptions ) {
                
                // This is the easiest way to have default options.
                var oSettings = $.extend(
                    {
                        // The user specifies the settings with the following options.
                        added_repeatable_field:     function() {},
                        removed_repeatable_field:   function() {},
                        sorted_fields:              function() {},
                        stopped_sorting_fields:     function() {},
                    }, 
                    oOptions 
                );

                // Store the callback functions
                $.fn.aAPFAddRepeatableFieldCallbacks.push( oSettings.added_repeatable_field );
                $.fn.aAPFRemoveRepeatableFieldCallbacks.push( oSettings.removed_repeatable_field );
                $.fn.aAPFSortedFieldsCallbacks.push( oSettings.sorted_fields );
                $.fn.aAPFStoppedSortingFieldsCallbacks.push( oSettings.stopped_sorting_fields );
                
                return;

            };
            
        }( jQuery ));";
        
    }

}
endif;