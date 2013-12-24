<?php
if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 */
class AdminPageFramework_InputField extends AdminPageFramework_Utility {
		
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $bIsMetaBox = false;
		
	protected static $_aStructure_FieldDefinition = array(
		'hfRenderField' => null,
		'hfGetScripts' => null,
		'hfGetStyles' => null,
		'hfGetIEStyles' => null,
		'hfFieldLoader' => null,
		'aEnqueueScripts' => null,
		'aEnqueueStyles' => null,
		'aDefaultKeys' => null,
	);
	
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldDefinition, &$oMsg ) {
			
		$this->aField = $aField + $aFieldDefinition['aDefaultKeys'] + self::$_aStructure_FieldDefinition;	// better not to merge recursively because some elements are array by default, not as multiple elements.
		$this->aFieldDefinition = $aFieldDefinition;
		$this->aOptions = $aOptions;
		$this->aErrors = $aErrors ? $aErrors : array();
		$this->oMsg = $oMsg;
			
		$this->sFieldName = $this->getInputFieldName();
		$this->sTagID = $this->getInputTagID( $aField );
		$this->vValue = $this->getInputFieldValue( $aField, $aOptions );
		
		// Global variable
		$GLOBALS['aAdminPageFramework']['aFieldFlags'] = isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'] )
			? $GLOBALS['aAdminPageFramework']['aFieldFlags'] 
			: array();
		
	}	
	
	/**
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Dropped the section key.
	 */
	private function getInputFieldName( $aField=null ) {
		
		$aField = isset( $aField ) ? $aField : $this->aField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $aField['name'] ) ) return $aField['name'];
		
		return isset( $aField['option_key'] ) // the meta box class does not use the option key
			? "{$aField['option_key']}[{$aField['page_slug']}][{$aField['field_id']}]"
			// ? "{$aField['option_key']}[{$aField['page_slug']}][{$aField['section_id']}][{$aField['field_id']}]"
			: $aField['field_id'];
		
	}

	private function getInputFieldValue( &$aField, $aOptions ) {	

		// If the value key is explicitly set, use it.
		if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $aField['page_slug'], $aField['section_id'] ) ) {			
		
			$vValue = $this->getInputFieldValueFromOptionTable( $aField, $aOptions );
			if ( $vValue != '' ) return $vValue;
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			$vValue = $this->getInputFieldValueFromPostTable( $_GET['post'], $aField );
			if ( $vValue != '' ) return $vValue;
			
		}
		
		// If the default value is set,
		if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}	
	private function getInputFieldValueFromOptionTable( &$aField, &$aOptions ) {
		
		if ( ! isset( $aOptions[ $aField['page_slug'] ][ $aField['field_id'] ] ) )
			return;
						
		$vValue = $aOptions[ $aField['page_slug'] ][ $aField['field_id'] ];
		
		// If it's not an array, return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$vDefault = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $vDefault, $sKey, '' );
		
		return $vValue;
			
		
	}	
	private function getInputFieldValueFromPostTable( $iPostID, &$aField ) {
		
		$vValue = get_post_meta( $iPostID, $aField['field_id'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
		
	}
		
	private function getInputTagID( $aField )  {
		
		// For Settings API's form fields should have these key values.
		if ( isset( $aField['section_id'], $aField['field_id'] ) )
			return "{$aField['section_id']}_{$aField['field_id']}";
			
		// For meta box form fields,
		if ( isset( $aField['field_id'] ) ) return $aField['field_id'];
		if ( isset( $aField['name'] ) ) return $aField['name'];	// the name key is for the input name attribute but it's better than nothing.
		
		// Not Found - it's not a big deal to have an empty value for this. It's just for the anchor link.
		return '';
			
	}		
	
	
	/** 
	 * Retrieves the input field HTML output.
	 * @since			2.0.0
	 * @since			2.1.6			Moved the repeater script outside the fieldset tag.
	 */ 
	public function getInputField( $sFieldType ) {
		
		// Prepend the field error message.
		$sOutput = isset( $this->aErrors[ $this->aField['field_id'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->aField['error_message']}" . $this->aErrors[ $this->aField['field_id'] ] . "</span><br />"
			: '';		
		
		// Prepare the field class selector 
		$this->sFieldClassSelector = $this->aField['repeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Add new elements
		$this->aField['sFieldName'] = $this->sFieldName;
		$this->aField['sTagID'] = $this->sTagID;
		$this->aField['sFieldClassSelector'] = $this->sFieldClassSelector;

		// Get the field output.
		$sOutput .= call_user_func_array( 
			$this->aFieldDefinition['hfRenderField'], 
			array( $this->vValue, $this->aField, $this->aOptions, $this->aErrors, $this->aFieldDefinition )
		);			
				
		// Add the description
		$sOutput .= ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>"
			: '';
			
		// Add the repeater script
		$sOutput .= $this->aField['repeatable']
			? $this->getRepeaterScript( $this->sTagID, count( ( array ) $this->vValue ) )
			: '';
			
		return $this->getRepeaterScriptGlobal( $this->sTagID )
			. "<fieldset>"
				. "<div class='admin-page-framework-fields'>"
					. $this->aField['before_field'] 
					. $sOutput
					. $this->aField['after_field']
				. "</div>"
			. "</fieldset>";
		
	}
	
	/**
	 * Sets or return the flag that indicates whether the creating fields are for meta boxes or not.
	 * 
	 * If the parameter is not set, it will return the stored value. Otherwise, it will set the value.
	 * 
	 * @since			2.1.2
	 */
	public function isMetaBox( $bTrueOrFalse=null ) {
		
		if ( isset( $bTrueOrFalse ) ) 
			$this->bIsMetaBox = $bTrueOrFalse;
			
		return $this->bIsMetaBox;
		
	}
	
	/**
	 * Indicates whether the repeatable fields script is called or not.
	 * 
	 * @since			2.1.3
	 */
	private $bIsRepeatableScriptCalled = false;
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	private function getRepeaterScript( $sTagID, $iFieldCount ) {

		$sAdd = $this->oMsg->__( 'add' );
		$sRemove = $this->oMsg->__( 'remove' );
		$sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$sButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$sAdd}' data-id='{$sTagID}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$sRemove}' {$sVisibility} data-id='{$sTagID}'>-</a>"
			. "</div>";

		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
				
					// Adds the buttons
					jQuery( '#{$sTagID} .admin-page-framework-field' ).append( \"{$sButtons}\" );
					
					// Update the fields
					updateAPFRepeatableFields( '{$sTagID}' );
					
				});
			</script>";
		
	}

	/**
	 * Returns the script that will be referred multiple times.
	 * since			2.1.3
	 */
	private function getRepeaterScriptGlobal( $sID ) {

		if ( $this->bIsRepeatableScriptCalled ) return '';
		$this->bIsRepeatableScriptCalled = true;
		return 
		"<script type='text/javascript'>
			jQuery( document ).ready( function() {
				
				// Global function literals
				
				// This function modifies the ids and names of the tags of input, textarea, and relevant tags for repeatable fields.
				updateAPFIDsAndNames = function( element, fIncrementOrDecrement ) {

					var updateID = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
							return '_' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}
					var updateName = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
							return '[' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}					
				
					element.attr( 'id', function( index, name ) { return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'name', function( index, name ){ return updateName( index, name ) } );
					
					// Color Pickers
					var nodeColorInput = element.find( 'input.input_color' );
					if ( nodeColorInput.length > 0 ) {
						
							var previous_id = nodeColorInput.attr( 'id' );
							
							if ( fIncrementOrDecrement > 0 ) {	// Add
					
								// For WP 3.5+
								var nodeNewColorInput = nodeColorInput.clone();	// re-clone without bind events.
								
								// For WP 3.4.x or below
								var sInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent';
								var sInputStyle = sInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
								
								nodeNewColorInput.val( sInputValue );	// set the default value	
								nodeNewColorInput.attr( 'style', sInputStyle );	// remove the background color set to the input field ( for WP 3.4.x or below )						 
								
								var nodeFarbtastic = element.find( '.colorpicker' );
								var nodeNewFarbtastic = nodeFarbtastic.clone();	// re-clone without bind elements.
								
								// Remove the old elements
								nodeIris = jQuery( '#' + previous_id ).closest( '.wp-picker-container' );	
								if ( nodeIris.length > 0 ) {	// WP 3.5+
									nodeIris.remove();	
								} else {
									jQuery( '#' + previous_id ).remove();	// WP 3.4.x or below
									element.find( '.colorpicker' ).remove();	// WP 3.4.x or below
								}
							
								// Add the new elements
								element.prepend( nodeNewFarbtastic );
								element.prepend( nodeNewColorInput );
								
							}
							
							element.find( '.colorpicker' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
							element.find( '.colorpicker' ).attr( 'rel', function( index, name ){ return updateID( index, name ) } );					

							// Renew the color picker script
							var cloned_id = element.find( 'input.input_color' ).attr( 'id' );
							registerAPFColorPickerField( cloned_id );					
					
					}

					// Image uploader buttons and image preview elements
					image_uploader_button = element.find( '.select_image' );
					if ( image_uploader_button.length > 0 ) {
						var previous_id = element.find( '.image-field input' ).attr( 'id' );
						image_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview img' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( image_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( image_uploader_button ).attr( 'data-enable_external_source' );
							setAPFImageUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Media uploader buttons
					media_uploader_button = element.find( '.select_media' );
					if ( media_uploader_button.length > 0 ) {
						var previous_id = element.find( '.media-field input' ).attr( 'id' );
						media_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( media_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( media_uploader_button ).attr( 'data-enable_external_source' );
							setAPFMediaUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Date pickers - somehow it needs to destroy the both previous one and the added one and assign the new date pickers 
					var date_picker_script = element.find( 'script.date-picker-enabler-script' );
					if ( date_picker_script.length > 0 ) {
						var previous_id = date_picker_script.attr( 'data-id' );
						date_picker_script.attr( 'data-id', function( index, name ){ return updateID( index, name ) } );

						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker( 'destroy' ); 
						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});						
						jQuery( '#' + previous_id ).datepicker( 'destroy' ); //here
						jQuery( '#' + previous_id ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});												
					}				
									
				}
				
				// This function is called from the updateAPFRepeatableFields() and from the media uploader for multiple file selections.
				addAPFRepeatableField = function( sFieldContainerID ) {	

					var field_container = jQuery( '#' + sFieldContainerID );
					var field_delimiter_id = sFieldContainerID.replace( 'field-', 'delimiter-' );
					var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
					
					var field_new = field_container.clone( true );
					var delimiter_new = field_delimiter.clone( true );
					var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;
			
					field_new.find( 'input,textarea' ).val( '' );	// empty the value		
					field_new.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
					field_new.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
					delimiter_new.insertAfter( target_element );	// add the delimiter
					field_new.insertAfter( target_element );		// add the cloned new field element

					// Increment the names and ids of the next following siblings.
					target_element.nextAll().each( function() {
						updateAPFIDsAndNames( jQuery( this ), true );
					});

					var remove_buttons =  field_container.closest( '.admin-page-framework-fields' ).find( '.repeatable-field-remove' );
					if ( remove_buttons.length > 1 ) 
						remove_buttons.show();				
					
					// Return the newly created element
					return field_new;
					
				}
				
				updateAPFRepeatableFields = function( sID ) {
				
					// Add button behaviour
					jQuery( '#' + sID + ' .repeatable-field-add' ).click( function() {
						
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						addAPFRepeatableField( field_container.attr( 'id' ) );
						return false;
						
					});		
					
					// Remove button behaviour
					jQuery( '#' + sID + ' .repeatable-field-remove' ).click( function() {
						
						// Need to remove two elements: the field container and the delimiter element.
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						var field_container_id = field_container.attr( 'id' );				
						var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
						var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
						var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;

						// Decrement the names and ids of the next following siblings.
						target_element.nextAll().each( function() {
							updateAPFIDsAndNames( jQuery( this ), false );	// the second parameter value indicates it's for decrement.
						});

						field_delimiter.remove();
						field_container.remove();
						
						var fieldsCount = jQuery( '#' + sID + ' .repeatable-field-remove' ).length;
						if ( fieldsCount == 1 ) {
							jQuery( '#' + sID + ' .repeatable-field-remove' ).css( 'display', 'none' );
						}
						return false;
					});
									
				}
			});
		</script>";
	}
	
}
endif;