<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Script_RepeatableField' ) ) :
/**
 * Provides JavaScript scripts for repeatable fields.
 * 
 * @since			3.0.0			
 * @package			AdminPageFramework
 * @subpackage		JavaScript
 * @internal
 */
class AdminPageFramework_Script_RepeatableField {

	static public function getjQueryPlugin( $sCannotAddMore, $sCannotRemoveMore ) {
		
		return "(function ( $ ) {
		
			$.fn.updateAPFRepeatableFields = function( aSettings ) {
				
				var nodeThis = this;	// it can be from a fields container or a cloned field container.
				var sFieldsContainerID = nodeThis.find( '.repeatable-field-add' ).first().data( 'id' );
				/* Store the fields specific options in an array  */
				if ( ! $.fn.aAPFRepeatableFieldsOptions ) $.fn.aAPFRepeatableFieldsOptions = [];
				if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {		
					$.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = $.extend({	
						max: 0,	// These are the defaults.
						min: 0,
						}, aSettings );
				}
				var aOptions = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ];
				
				/* Set the option values in the data attributes so that when a section is repeated and creates a brand new field container, it can refer the options */
				$( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-max', aOptions['max'] );
				$( nodeThis ).find( '.admin-page-framework-repeatable-field-buttons' ).attr( 'data-min', aOptions['min'] );
				
				/* The Add button behaviour - if the tag id is given, multiple buttons will be selected. 
				 * Otherwise, a field node is given and single button will be selected. */
				$( nodeThis ).find( '.repeatable-field-add' ).unbind( 'click' );
				$( nodeThis ).find( '.repeatable-field-add' ).click( function() {
					$( this ).addAPFRepeatableField();
					return false;	// will not click after that
				});
				
				/* The Remove button behaviour */
				$( nodeThis ).find( '.repeatable-field-remove' ).unbind( 'click' );
				$( nodeThis ).find( '.repeatable-field-remove' ).click( function() {
					$( this ).removeAPFRepeatableField();
					return false;	// will not click after that
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

				var nodeFieldContainer = $( '#' + sFieldContainerID );
				var nodeNewField = nodeFieldContainer.clone();	// clone without bind events.
				var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );
				var sFieldsContainerID = nodeFieldsContainer.attr( 'id' );

				/* If the set maximum number of fields already exists, do not add */
 				if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {		
					var nodeButtonContainer = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' );
					$.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = {	
						max: nodeButtonContainer.attr( 'data-max' ),	// These are the defaults.
						min: nodeButtonContainer.attr( 'data-min' ),
					};
				}		 
				var sMaxNumberOfFields = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['max'];
				if ( sMaxNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length >= sMaxNumberOfFields ) {
					var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotAddMore}', sMaxNumberOfFields );
					var nodeMessage = $( '<span class=\"repeatable-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 )
						nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}
				
				nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value		
				nodeNewField.find( '.repeatable-error' ).remove();	// remove error messages.
				
				/* Add the cloned new field element */
				nodeNewField.insertAfter( nodeFieldContainer );	
				
				/* Increment the names and ids of the next following siblings. */
				nodeFieldContainer.nextAll().each( function() {
					$( this ).incrementIDAttribute( 'id' );
					$( this ).find( 'label' ).incrementIDAttribute( 'for' );
					$( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
					$( this ).find( 'input,textarea,select' ).incrementNameAttribute( 'name' );
				});

				/* Rebind the click event to the buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. 
				 * Also do this after updating the attributes since the script needs to check the last added id for repeatable field options such as 'min'
				 * */
				nodeNewField.updateAPFRepeatableFields();
				
				/* It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. */
				nodeFieldContainer.find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
				
				/* Call the registered callback functions */
				nodeNewField.callBackAddRepeatableField( nodeNewField.data( 'type' ), nodeNewField.attr( 'id' ) );					
				
				/* If more than one fields are created, show the Remove button */
				var nodeRemoveButtons =  nodeFieldsContainer.find( '.repeatable-field-remove' );
				if ( nodeRemoveButtons.length > 1 ) nodeRemoveButtons.show();				
									
				/* Return the newly created element */
				return nodeNewField;	// media uploader needs this 
				
			};
				
			$.fn.removeAPFRepeatableField = function() {
				
				/* Need to remove the element: the field container */
				var nodeFieldContainer = $( this ).closest( '.admin-page-framework-field' );
				var nodeFieldsContainer = $( this ).closest( '.admin-page-framework-fields' );
				var sFieldsContainerID = nodeFieldsContainer.attr( 'id' );
				
				/* If the set minimum number of fields already exists, do not remove */
				var sMinNumberOfFields = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ]['min'];
				if ( sMinNumberOfFields != 0 && nodeFieldsContainer.find( '.admin-page-framework-field' ).length <= sMinNumberOfFields ) {
					var nodeLastRepeaterButtons = nodeFieldContainer.find( '.admin-page-framework-repeatable-field-buttons' ).last();
					var sMessage = $( this ).formatPrintText( '{$sCannotRemoveMore}', sMinNumberOfFields );
					var nodeMessage = $( '<span class=\"repeatable-error\" id=\"repeatable-error-' + sFieldsContainerID + '\" style=\"float:right;color:red;margin-left:1em;\">' + sMessage + '</span>' );
					if ( nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).length > 0 )
						nodeFieldsContainer.find( '#repeatable-error-' + sFieldsContainerID ).replaceWith( nodeMessage );
					else
						nodeLastRepeaterButtons.before( nodeMessage );
					nodeMessage.delay( 2000 ).fadeOut( 1000 );
					return;		
				}				
				
				/* Decrement the names and ids of the next following siblings. */
				nodeFieldContainer.nextAll().each( function() {
					$( this ).decrementIDAttribute( 'id' );
					$( this ).find( 'label' ).decrementIDAttribute( 'for' );
					$( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id' );
					$( this ).find( 'input,textarea,select' ).decrementNameAttribute( 'name' );																	
				});

				/* Call the registered callback functions */
				nodeFieldContainer.callBackRemoveRepeatableField( nodeFieldContainer.data( 'type' ), nodeFieldContainer.attr( 'id' ) );	
			
				/* Remove the field */
				nodeFieldContainer.remove();
				
				/* Count the remaining Remove buttons and if it is one, disable the visibility of it */
				var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove' );
				if ( nodeRemoveButtons.length == 1 ) nodeRemoveButtons.css( 'display', 'none' );
					
			};
				
		}( jQuery ));	
		";
		
	}

}
endif;