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
		
	// protected static $_aStructure_FieldDefinition = array(
		// 'sSlug'	=> null,
		// 'hfRenderField' => null,
		// 'hfGetScripts' => null,
		// 'hfGetStyles' => null,
		// 'hfGetIEStyles' => null,
		// 'hfFieldLoader' => null,
		// 'aEnqueueScripts' => null,
		// 'aEnqueueStyles' => null,
		// 'aDefaultKeys' => null,
	// );
	
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg ) {
			
		$aFieldTypeDefinition = isset( $aFieldTypeDefinitions[ $aField['type'] ] ) ? $aFieldTypeDefinitions[ $aField['type'] ] : $aFieldTypeDefinitions['default'];
		$this->aField = $this->uniteArrays( $aField, $aFieldTypeDefinition['aDefaultKeys'] );
		$this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
		$this->aOptions = $aOptions;
		$this->aErrors = $aErrors ? $aErrors : array();
		$this->oMsg = $oMsg;
				
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
	private function _getInputFieldName( $aField=null ) {
		
		$aField = isset( $aField ) ? $aField : $this->aField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $aField['name'] ) ) return $aField['name'];
		
		return isset( $aField['option_key'] ) // the meta box class does not use the option key
			? "{$aField['option_key']}[{$aField['page_slug']}][{$aField['field_id']}]"
			: $aField['field_id'];
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Removed the check of the 'value' and 'default' keys.
	 */
	private function _getInputFieldValue( &$aField, $aOptions ) {	

		// If the value key is explicitly set, use it.
		// if ( isset( $aField['vValue'] ) ) return $aField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $aField['page_slug'], $aField['section_id'] ) ) {			
		
			return $this->_getInputFieldValueFromOptionTable( $aField, $aOptions );
			
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			return $this->_getInputFieldValueFromPostTable( $_GET['post'], $aField );
			
			
		}
		
		// If the default value is set,
		// if ( isset( $aField['default'] ) ) return $aField['default'];
		
	}	
	
	/**
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Dropped the check of default values.
	 */
	private function _getInputFieldValueFromOptionTable( &$aField, &$aOptions ) {
		
		if ( ! isset( $aOptions[ $aField['page_slug'] ][ $aField['field_id'] ] ) )
			return;
						
		return $aOptions[ $aField['page_slug'] ][ $aField['field_id'] ];
		
/* // If it's not an array, return it.
if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;

// If it's an array, check if there is an empty value in each element.
$vDefault = isset( $aField['default'] ) ? $aField['default'] : array(); 
foreach ( $vValue as $sKey => &$sElement ) 
	if ( $sElement == '' )
		$sElement = $this->getCorrespondingArrayValue( $vDefault, $sKey, '' );

return $vValue;
 */			
		
	}	
	/**
	 * 
	 * @since			2.0.0
	 * @subce			3.0.0			Dropped the check of default values
	 */
	private function _getInputFieldValueFromPostTable( $iPostID, &$aField ) {
		
		return get_post_meta( $iPostID, $aField['field_id'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$default = isset( $aField['default'] ) ? $aField['default'] : array(); 
		foreach ( $vValue as $sKey => &$sElement ) 
			if ( $sElement == '' )
				$sElement = $this->getCorrespondingArrayValue( $default, $sKey, '' );
		
		return $vValue;
		
	}
		
	private function _getInputTagID( $aField )  {
		
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
	public function _getInputFieldOutput() {
		
		$aOutput = array();
		
		// Prepend the field error message.
		$aOutput[] = isset( $this->aErrors[ $this->aField['field_id'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->aField['error_message']}" . $this->aErrors[ $this->aField['field_id'] ] . "</span><br />"
			: '';		
		
		// Prepare the field class selector 
		$sFieldClassSelector = $this->aField['is_repeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Set new elements
		$this->aField['field_name'] = $this->_getInputFieldName( $this->aField );
		$this->aField['tag_id'] = $this->_getInputTagID( $this->aField );
		$this->aField['field_class_selector'] = $sFieldClassSelector;
			
		// Compose fields array for sub-fields	
		$aFields = $this->_composeFieldsArray( $this->aField, $this->aOptions );
		
		// Get the field output.
		foreach( $aFields as $sKey => $aField ) {
			$aField['index'] = $sKey;			
// var_dump( $aField );			
			$aFieldTypeDefinition = isset( $this->aFieldTypeDefinitions[ $aField['type'] ] )
				? $this->aFieldTypeDefinitions[ $aField['type'] ] 
				: $this->aFieldTypeDefinitions['default'];
			$aOutput[] = is_callable( $aFieldTypeDefinition['hfRenderField'] ) 
				? call_user_func_array(
					$aFieldTypeDefinition['hfRenderField'],
					array( $aField )
				)
				: "";

		}
				
		// Add the description
		$aOutput[] = ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>"
			: '';
			
		// Add the repeater script
		$aOutput[] = $this->aField['is_repeatable']
			? $this->_getRepeaterScript( $this->aField['tag_id'], count( $aFields ) )
			: '';

		return $this->getRepeaterScriptGlobal( $this->aField['tag_id'] )
			. "<fieldset>"
				. "<div class='admin-page-framework-fields' id='{$this->aField['tag_id']}'>"
					. $this->aField['before_field'] 
					. implode( PHP_EOL, $aOutput )
					. $this->aField['after_field']
				. "</div>"
			. "</fieldset>";
		
	}
	
		/**
		 * Returns the array of fields 
		 * 
		 * @since			3.0.0
		 */
		protected function _composeFieldsArray( $aField, $aOptions ) {

			/* Get the set value(s) */
			$vSavedValue = $this->_getInputFieldValue( $aField, $aOptions );
		
			/* Separate the first field and sub-fields */
			$aFirstField = array();
			$aSubFields = array();
			foreach( $aField as $nsIndex => $vFieldElement ) {
				if ( is_numeric( $nsIndex ) ) 
					$aSubFields[] = $vFieldElement;
				else 
					$aFirstField[ $nsIndex ] = $vFieldElement;
			}		
			
			/* Create the sub-fields of repeatable fields based on the saved values */
			if ( $aField['is_repeatable'] ) 
				foreach( ( array ) $vSavedValue as $iIndex => $vValue ) {
					if ( $iIndex == 0 ) continue;
					$aSubFields[ $iIndex - 1 ] = isset( $aSubFields[ $iIndex - 1 ] ) && is_array( $aSubFields[ $iIndex - 1 ] ) 
						? $aSubFields[ $iIndex - 1 ] 
						: array();			
				}
			
			/* Put the initial field and the sub-fields together in one array */
			foreach( $aSubFields as &$aSubField ) 
				$aSubField = $aSubField + $aFirstField;
			$aFields = array_merge( array( $aFirstField ), $aSubFields );
					
			/* Set the saved values */		
			if ( count( $aSubFields ) > 0 || $aField['is_repeatable'] || $aField['is_sortable'] ) {	// means the elements are saved in an array.
				foreach( $aFields as $iIndex => &$aThisField ) {
					$aThisField['saved_value'] = isset( $vSavedValue[ $iIndex ] ) ? $vSavedValue[ $iIndex ] : null;
					$aThisField['is_multiple'] = true;
				}
			} else {
				$aFields[ 0 ]['saved_value'] = $vSavedValue;
				$aFields[ 0 ]['is_multiple'] = false;
			} 

			/* Determine the value */
			unset( $aThisField );	// PHP requires this for a previously used variable as reference.
			foreach( $aFields as &$aThisField ) 
				$aThisField['value'] = isset( $aThisField['value'] ) 
					? $aThisField['value'] 
					: ( isset( $aThisField['saved_value'] ) 
						? $aThisField['saved_value']
						: ( isset( $aThisField['default'] )
							? $aThisField['default']
							: null
						)
					);

			return $aFields;
			
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
	private function _getRepeaterScript( $tag_id, $iFieldCount ) {

		$sAdd = $this->oMsg->__( 'add' );
		$sRemove = $this->oMsg->__( 'remove' );
		$sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$sButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$sAdd}' data-id='{$tag_id}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$sRemove}' {$sVisibility} data-id='{$tag_id}'>-</a>"
			. "</div>";

		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
				
					// Adds the buttons
					jQuery( '#{$tag_id} .admin-page-framework-field' ).append( \"{$sButtons}\" );
					
					// Update the fields
					updateAPFRepeatableFields( '{$tag_id}' );
					
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