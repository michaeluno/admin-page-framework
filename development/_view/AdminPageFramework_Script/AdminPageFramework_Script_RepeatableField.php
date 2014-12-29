<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for repeatable fields.
 * 
 * @since       3.0.0     
 * @since       3.3.0       Extends `AdminPageFramework_Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Script_RepeatableField extends AdminPageFramework_Script_Base {

    /**
     * Returns the script.
     * 
     * @since       3.0.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     */
    static public function getScript() {

        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];            
        $sCannotAddMore     = $_oMsg->get( 'allowed_maximum_number_of_fields' );
        $sCannotRemoveMore  = $_oMsg->get( 'allowed_minimum_number_of_fields' );
        
        return <<<JAVASCRIPTS
(function ( $ ) {
        
    $.fn.updateAPFRepeatableFields = function( aSettings ) {
        
        var nodeThis            = this; // it can be from a fields container or a cloned field container.
        var sFieldsContainerID  = nodeThis.find( '.repeatable-field-add' ).first().data( 'id' );
        
        /* Store the fields specific options in an array  */
        if ( ! $.fn.aAPFRepeatableFieldsOptions ) $.fn.aAPFRepeatableFieldsOptions = [];
        if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {     
            $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = $.extend({    
                max: 0, // These are the defaults.
                min: 0,
                }, aSettings );
        }
        var aOptions = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ];
        
        /* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer the options */
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-max', aOptions['max'] );
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-min', aOptions['min'] );
        
        /* The Add button behavior - if the tag id is given, multiple buttons will be selected. 
         * Otherwise, a field node is given and single button will be selected. */
        $( nodeThis ).find( '.repeatable-field-add' ).unbind( 'click' );
        $( nodeThis ).find( '.repeatable-field-add' ).click( function() {
            $( this ).addAPFRepeatableField();
            return false; // will not click after that
        });
        
        /* The Remove button behavior */
        $( nodeThis ).find( '.repeatable-field-remove' ).unbind( 'click' );
        $( nodeThis ).find( '.repeatable-field-remove' ).click( function() {
            $( this ).removeAPFRepeatableField();
            return false; // will not click after that
        });     
        
        /* If the number of fields is less than the set minimum value, add fields. */
        var sFieldID = nodeThis.find( '.repeatable-field-add' ).first().closest( '.admin-page-framework-field' ).attr( 'id' );
        var nCurrentFieldCount = jQuery( '#' + sFieldsContainerID ).find( '.admin-page-framework-field' ).length;
        if ( aOptions['min'] > 0 && nCurrentFieldCount > 0 ) {
            if ( ( aOptions['min'] - nCurrentFieldCount ) > 0 ) {     
                $( '#' + sFieldID ).addAPFRepeatableField( sFieldID );  
            }
        }
        
    };
    
    /**
     * Adds a repeatable field.
     */
    $.fn.addAPFRepeatableField = function( sFieldContainerID ) {
        if ( typeof sFieldContainerID === 'undefined' ) {
            var sFieldContainerID = $( this ).closest( '.admin-page-framework-field' ).attr( 'id' );    
        }

        var nodeFieldContainer  = $( '#' + sFieldContainerID );
        var nodeNewField        = nodeFieldContainer.clone(); // clone without bind events.
        var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
        var sFieldsContainerID  = nodeFieldsContainer.attr( 'id' );

        /* If the set maximum number of fields already exists, do not add */
         if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {     
            var nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' );
            $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = {    
                max: nodeButtonContainer.attr( 'data-max' ), // These are the defaults.
                min: nodeButtonContainer.attr( 'data-min' ),
            };
        }  
        var sMaxNumberOfFields = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['max'];
        if ( sMaxNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length >= sMaxNumberOfFields ) {
            var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" >' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            nodeMessage.delay( 2000 ).fadeOut( 1000 );
            return;     
        }
        
        nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value     
        nodeNewField.find( '.repeatable-error' ).remove(); // remove error messages.
        
        /* Add the cloned new field element */
        nodeNewField.insertAfter( nodeFieldContainer );    
        
        /* Increment the names and ids of the next following siblings. */
        nodeFieldContainer.nextAll().each( function() {
            $( this ).incrementIDAttribute( 'id' );
            $( this ).find( 'label' ).incrementIDAttribute( 'for' );
            $( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
            $( this ).find( 'input:not(.apf_checkbox),textarea,select' ).incrementNameAttribute( 'name' );
            $( this ).find( 'input.apf_checkbox' ).incrementNameAttribute( 'name', -2 ); // for checkboxes, increment the second found digit from the end 
        });

        /* Rebind the click event to the buttons - important to update AFTER inserting the clone to the document node since the update method needs to count the fields. 
         * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'.
         * */
        nodeNewField.updateAPFRepeatableFields();
        
        /* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
        nodeFieldContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'checked' );    
        
        /* Call the registered callback functions */
        nodeNewField.callBackAddRepeatableField( nodeNewField.data( 'type' ), nodeNewField.attr( 'id' ), 0, 0, 0 );     
        
        /* If more than one fields are created, show the Remove button */
        var nodeRemoveButtons =  nodeFieldsContainer.find( '.repeatable-field-remove' );
        if ( nodeRemoveButtons.length > 1 ) { nodeRemoveButtons.css( 'visibility', 'visible' ); }
                            
        /* Return the newly created element */
        return nodeNewField; // media uploader needs this 
        
    };
        
    $.fn.removeAPFRepeatableField = function() {
        
        /* Need to remove the element: the field container */
        var nodeFieldContainer  = $( this ).closest( '.admin-page-framework-field' );
        var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
        var sFieldsContainerID  = nodeFieldsContainer.attr( 'id' );
        
        /* If the set minimum number of fields already exists, do not remove */
        var sMinNumberOfFields  = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['min'];
        if ( sMinNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length <= sMinNumberOfFields ) {
            var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + sFieldsContainerID + '\">' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            nodeMessage.delay( 2000 ).fadeOut( 1000 );
            return;     
        }     
        
        /* Decrement the names and ids of the next following siblings. */
        nodeFieldContainer.nextAll().each( function() {
            $( this ).decrementIDAttribute( 'id' );
            $( this ).find( 'label' ).decrementIDAttribute( 'for' );
            $( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id' );
            $( this ).find( 'input:not(.apf_checkbox),textarea,select' ).decrementNameAttribute( 'name' );     
            $( this ).find( 'input.apf_checkbox' ).decrementNameAttribute( 'name', -2 ); // for checkboxes, increment the second found digit from the end                     
            
        });

        /* Store the next field */
        var oNextField = nodeFieldContainer.next();

        /* Remove the field */
        nodeFieldContainer.remove();
        
        /** 
         * Call the registered callback functions
         * 
         * @since 3.0.0
         * @since 3.1.0 Changed it to do after removing the element and passing the next field element to the first parameter of the callback.
         */
        oNextField.callBackRemoveRepeatableField( 
            nodeFieldContainer.data( 'type' ), 
            nodeFieldContainer.attr( 'id' ), 
            0,  // call type 0: fields, 1: sections
            0,  // section index
            0   // field index
        );    
        
        /* Count the remaining Remove buttons and if it is one, disable the visibility of it */
        var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove' );
        if ( 1 === nodeRemoveButtons.length ) { nodeRemoveButtons.css( 'visibility', 'hidden' ); }
            
    };
        
}( jQuery ));    
JAVASCRIPTS;
        
    }

}