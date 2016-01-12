<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides JavaScript scripts for repeatable fields.
 * 
 * @since       3.0.0     
 * @since       3.3.0       Extends `AdminPageFramework_Form_View___Script_Base`.
 * @package     AdminPageFramework
 * @subpackage  JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_RepeatableField extends AdminPageFramework_Form_View___Script_Base {

    /**
     * Returns an inline JavaScript script.
     * 
     * @since       3.2.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */        
    static public function getScript( /* $oMsg */ ) {

        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];            
        $sCannotAddMore     = $_oMsg->get( 'allowed_maximum_number_of_fields' );
        $sCannotRemoveMore  = $_oMsg->get( 'allowed_minimum_number_of_fields' );
        
        return <<<JAVASCRIPTS
(function ( $ ) {
        
    /**
     * 
     * @remark      This method can be called from a fields container or a cloned field container.
     */
    $.fn.updateAdminPageFrameworkRepeatableFields = function( aSettings ) {
        
        var nodeThis            = this; 
        var sFieldsContainerID  = nodeThis.find( '.repeatable-field-add-button' ).first().data( 'id' );
        
        /* Store the fields specific options in an array  */
        if ( ! $.fn.aAdminPageFrameworkRepeatableFieldsOptions ) {
            $.fn.aAdminPageFrameworkRepeatableFieldsOptions = [];
        }
        if ( ! $.fn.aAdminPageFrameworkRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {     
            $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ] = $.extend({    
                max: 0, // These are the defaults.
                min: 0,
                fadein: 500,
                fadeout: 500,
                }, aSettings );
        }
        var _aOptions = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ];

        /* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer to the options */
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-max', _aOptions[ 'max' ] );
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-min', _aOptions[ 'min' ] );
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-fadein', _aOptions[ 'fadein' ] );
        $( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-fadeout', _aOptions[ 'fadeout' ] );
        
        /* The Add button behavior - if the tag id is given, multiple buttons will be selected. 
         * Otherwise, a field node is given and single button will be selected. */
        $( nodeThis ).find( '.repeatable-field-add-button' ).unbind( 'click' );
        $( nodeThis ).find( '.repeatable-field-add-button' ).click( function() {
            $( this ).addAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });
        
        /* The Remove button behavior */
        $( nodeThis ).find( '.repeatable-field-remove-button' ).unbind( 'click' );
        $( nodeThis ).find( '.repeatable-field-remove-button' ).click( function() {
            $( this ).removeAdminPageFrameworkRepeatableField();
            return false; // will not click after that
        });     
        
        /* If the number of fields is less than the set minimum value, add fields. */
        var _sFieldID           = nodeThis.find( '.repeatable-field-add-button' ).first().closest( '.admin-page-framework-field' ).attr( 'id' );
        var _nCurrentFieldCount = jQuery( '#' + sFieldsContainerID ).find( '.admin-page-framework-field' ).length;
        if ( _aOptions[ 'min' ] > 0 && _nCurrentFieldCount > 0 ) {
            if ( ( _aOptions[ 'min' ] - _nCurrentFieldCount ) > 0 ) {     
                $( '#' + _sFieldID ).addAdminPageFrameworkRepeatableField( _sFieldID );  
            }
        }
        
    };
    
    /**
     * Adds a repeatable field.
     */
    $.fn.addAdminPageFrameworkRepeatableField = function( sFieldContainerID ) {
        
        if ( 'undefined' === typeof sFieldContainerID ) {
            var sFieldContainerID = $( this ).closest( '.admin-page-framework-field' ).attr( 'id' );    
        }

        var nodeFieldContainer  = $( '#' + sFieldContainerID );
        var nodeNewField        = nodeFieldContainer.clone(); // clone without bind events.
        var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
        var sFieldsContainerID  = nodeFieldsContainer.attr( 'id' );

        // If the set maximum number of fields already exists, do not add.
        if ( ! $.fn.aAdminPageFrameworkRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {     
            var nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' );
            $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ] = {    
                max: nodeButtonContainer.attr( 'data-max' ), // These are the defaults.
                min: nodeButtonContainer.attr( 'data-min' ),
                fadein: nodeButtonContainer.attr( 'data-fadein' ),
                fadeout: nodeButtonContainer.attr( 'data-fadeout' ),
            };
        }  
       
        var _iFadein  = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadein' ];
        var _iFadeout = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadeout' ];

        // Show a warning message if the user tries to add more fields than the number of allowed fields.
        var sMaxNumberOfFields = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ]['max'];
        if ( sMaxNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length >= sMaxNumberOfFields ) {
            var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" >' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }
        
        // Empty values.
        nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value     
        nodeNewField.find( 'input[type=checkbox]' ).prop( 'checked', false ); // uncheck checkboxes.
        nodeNewField.find( '.repeatable-error' ).remove(); // remove error messages.
        
        // Add the cloned new field element.
        if ( _iFadein ) {
            nodeNewField
                .hide()
                .insertAfter( nodeFieldContainer )
                .delay( 100 )
                .fadeIn( _iFadein );
        } else {            
            nodeNewField.insertAfter( nodeFieldContainer );    
        }

        /* Increment the names and ids of the next following siblings. */
        // @deprecated 3.6.0
        // nodeFieldContainer.nextAll().each( function() {
            // $( this ).incrementIDAttribute( 'id' );
            // $( this ).find( 'label' ).incrementIDAttribute( 'for' );
            // $( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
            // $( this ).find( 'input:not(.apf_checkbox),textarea,select' ).incrementNameAttribute( 'name' );
            // $( this ).find( 'input.apf_checkbox' ).incrementNameAttribute( 'name', -2 ); // for checkboxes, increment the second found digit from the end 
        // }); 

        // 3.6.0+ Increment name and id attributes of the newly cloned field.
        // Increment the count
        var _iFieldCount            = Number( nodeFieldsContainer.attr( 'data-largest_index' ) );
        var _iIncrementedFieldCount = _iFieldCount + 1;
        nodeFieldsContainer.attr( 'data-largest_index', _iIncrementedFieldCount );
     
        var _sFieldTagIDModel    = nodeFieldsContainer.attr( 'data-field_tag_id_model' );
        var _sFieldNameModel     = nodeFieldsContainer.attr( 'data-field_name_model' );
        var _sFieldFlatNameModel = nodeFieldsContainer.attr( 'data-field_name_flat_model' );
        var _sFieldAddressModel  = nodeFieldsContainer.attr( 'data-field_address_model' );
        
        // nodeNewField.incrementIDAttribute( 'id' ); // @deprecated 3.6.0
        nodeNewField.incrementAttribute(
            'id', // attribute name
            _iFieldCount, // increment from
            _sFieldTagIDModel // digit model
        );
        // nodeNewField.find( 'label' ).incrementIDAttribute( 'for' ); // @deprecated 3.6.0
        nodeNewField.find( 'label' ).incrementAttribute(
            'for', // attribute name
            _iFieldCount, // increment from
            _sFieldTagIDModel // digit model
        );
        // nodeNewField.find( 'input,textarea,select' ).incrementIDAttribute( 'id' ); // @deprecated 3.6.0
        nodeNewField.find( 'input,textarea,select' ).incrementAttribute(
            'id', // attribute name
            _iFieldCount, // increment from
            _sFieldTagIDModel // digit model
        );       
        // nodeNewField.find( 'input:not(.apf_checkbox),textarea,select' ).incrementNameAttribute( 'name' );
        nodeNewField.find( 'input,textarea,select' ).incrementAttribute(
            'name', // attribute name
            _iFieldCount, // increment from
            _sFieldNameModel // digit model
        );
        
        // Update the hidden input elements that contain field names for nested elements.
        nodeNewField.find( 'input[type=hidden].element-address' ).incrementAttributes(
            [ 'name', 'value' ], // attribute names - this elements contains id values in the 'name' attribute.
            _iFieldCount,
            _sFieldAddressModel // digit model - this is
        );  
               
        /* Rebind the click event to the + and - buttons - important to update AFTER inserting the clone to the document node since the update method needs to count the fields. 
         * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'.
         * */
        nodeNewField.updateAdminPageFrameworkRepeatableFields();
        
        // It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone.
        nodeFieldContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'checked' );
        
        // Call back the registered functions.
        // nodeNewField.callBackAddRepeatableField( nodeNewField.data( 'type' ), nodeNewField.attr( 'id' ), 0, 0, 0 );
        nodeNewField.trigger( 
            'admin_page_framework_repeated_field', 
            [ 
                nodeNewField.data( 'type' ), // field type slug
                nodeNewField.attr( 'id' ),   // element tag id
                0, // call type 
                0, // section index - @todo find the section index
                0  // field index - @todo find the field index
            ]
        );
        
        // For nested fields,
        $( nodeNewField ).find( '.admin-page-framework-field' ).each( function( iIterationIndex ) {    
        
            // Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node 
            // since the update method need to count fields.
            // @todo examine if this is needed any longer.
            $( this ).updateAdminPageFrameworkRepeatableFields();
                                        
            // Call back the registered functions.
            $( this ).trigger( 
                'admin_page_framework_repeated_field', 
                [ 
                    $( this ).data( 'type' ), 
                    nodeNewField.attr( 'id' ), // pass the parent field id
                    2,  // call type, 0 : repeatable sections, 1: repeatable fields, 2: nested repeatable fields.
                    0,  // @todo find the section index
                    iIterationIndex  // @todo find the nested field index
                ]
            );            
            
        });    
        
        // If more than one fields are created, show the Remove button.
        var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove-button' );
        if ( nodeRemoveButtons.length > 1 ) { 
            nodeRemoveButtons.css( 'visibility', 'visible' ); 
        }
                            
        // Return the newly created element. The media uploader needs this 
        return nodeNewField; 
        
    };
        
    $.fn.removeAdminPageFrameworkRepeatableField = function() {
        
        /* Need to remove the element: the field container */
        var nodeFieldContainer  = $( this ).closest( '.admin-page-framework-field' );
        var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
        var sFieldsContainerID  = nodeFieldsContainer.attr( 'id' );
        
        /* If the set minimum number of fields already exists, do not remove */
        var sMinNumberOfFields  = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'min' ];
        if ( sMinNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length <= sMinNumberOfFields ) {
            var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
            var sMessage                = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfFields );
            var nodeMessage             = $( '<span class=\"repeatable-error repeatable-field-error\" id=\"repeatable-error-' + sFieldsContainerID + '\">' + sMessage + '</span>' );
            if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 ) {
                nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
            } else {
                nodeLastRepeaterButtons.before( nodeMessage );
            }
            var _iFadeout = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadeout' ]
                ? $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadeout' ]
                : 500;
            nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }     
        
        /* Decrement the names and ids of the next following siblings. */
        // @deprecated      3.6.0
        // nodeFieldContainer.nextAll().each( function() {
            // $( this ).decrementIDAttribute( 'id' );
            // $( this ).find( 'label' ).decrementIDAttribute( 'for' );
            // $( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id' );
            // $( this ).find( 'input:not(.apf_checkbox),textarea,select' ).decrementNameAttribute( 'name' );     
            // $( this ).find( 'input.apf_checkbox' ).decrementNameAttribute( 'name', -2 ); // for checkboxes, increment the second found digit from the end                     
        // });

        /* The next field */
        // @deprecated 3.6.0
        // var _oNextFieldoNextField = nodeFieldContainer.next();

        /* Remove the field */
        // nodeFieldContainer.remove(); // @deprecated  3.6.0
        var _iFadeout = $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadeout' ]
            ? $.fn.aAdminPageFrameworkRepeatableFieldsOptions[ sFieldsContainerID ][ 'fadeout' ]
            : 500;        
        nodeFieldContainer.fadeOut( _iFadeout, function() { 
            $( this ).remove(); 
            var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove-button' );
            if ( 1 === nodeRemoveButtons.length ) { 
                nodeRemoveButtons.css( 'visibility', 'hidden' ); 
            }            
        } );
        
        /** 
         * Call the registered callback functions
         * 
         * @since           3.0.0
         * @since           3.1.0 Changed it to do after removing the element and passing the next field element to the first parameter of the callback.
         * @deprecated      3.6.0
         */
        // _oNextField.callBackRemoveRepeatableField( 
            // nodeFieldContainer.data( 'type' ), 
            // nodeFieldContainer.attr( 'id' ), 
            // 0,  // call type 0: fields, 1: sections
            // 0,  // section index
            // 0   // field index
        // );    
        
        /* Count the remaining Remove buttons and if it is one, disable the visibility of it */
        // @deprecated  3.6.0   Moved to the above fadeOut method.
        // var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove-button' );
        // if ( 1 === nodeRemoveButtons.length ) { 
            // nodeRemoveButtons.css( 'visibility', 'hidden' ); 
        // }
            
    };
        
}( jQuery ));    
JAVASCRIPTS;
        
    }

}
