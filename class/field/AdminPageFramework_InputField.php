<?php
if ( ! class_exists( 'AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @since			2.1.5			Separated the methods that defines field types to different classes.
 * @extends			AdminPageFramework_Utility
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 */
class AdminPageFramework_InputField extends AdminPageFramework_WPUtility {
		
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $_bIsMetaBox = false;
			
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg ) {

		/* 1. Set up the properties that will be accessed later in the methods. */
		$aFieldTypeDefinition = isset( $aFieldTypeDefinitions[ $aField['type'] ] ) ? $aFieldTypeDefinitions[ $aField['type'] ] : $aFieldTypeDefinitions['default'];
		
		/* 
		 * 1-1. Set up the 'attributes' array - the 'attributes' element is dealt separately as it contains some overlapping elements with the regular elements such as 'value'.
		 * There are required keys in the attributes array: 'fieldset', 'fields', and 'field'; these should not be removed here.
		 */
		$aFieldTypeDefinition['aDefaultKeys']['attributes'] = array(	
			'fieldset'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldset'],
			'fields'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['fields'],
			'field'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['field'],
		);	
		$this->aField = $this->uniteArrays( $aField, $aFieldTypeDefinition['aDefaultKeys'] );
		
		/* 1-2. Store the other properties */
		$this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
		$this->aOptions = $aOptions;
		$this->aErrors = $aErrors ? $aErrors : array();
		$this->oMsg = $oMsg;
				
		/* 2. Load necessary JavaScript scripts */
		$this->_loadScripts();
		
	}	
		/**
		 * Inserts necessary JavaScript scripts for fields.
		 * @since			3.0.0
		 */
		private function _loadScripts() {
			
			// Global variable
			$GLOBALS['aAdminPageFramework']['aFieldFlags'] = isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'] )
				? $GLOBALS['aAdminPageFramework']['aFieldFlags']
				: array();
			
			if ( ! isset( $GLOBALS['aAdminPageFramework']['bEnqueuedUtilityPluins'] ) ) {
				add_action( 'admin_footer', array( $this, '_replyToAddUtilityPlugins' ) );
				add_action( 'admin_footer', array( $this, '_replyToAddAttributeUpdaterjQueryPlugin' ) );
				$GLOBALS['aAdminPageFramework']['bEnqueuedUtilityPluins'] = true;
			}
			if ( ! isset( $GLOBALS['aAdminPageFramework']['bEnqueuedRepeatableFieldScript'] ) ) {
				add_action( 'admin_footer', array( $this, '_replyToAddRepeatableFieldjQueryPlugin' ) );
				$GLOBALS['aAdminPageFramework']['bEnqueuedRepeatableFieldScript'] = true;
			}
			if ( ! isset( $GLOBALS['aAdminPageFramework']['bEnqueuedSortableFieldScript'] ) ) {
				add_action( 'admin_footer', array( $this, '_replyToAddSortableFieldPlugin' ) );
				$GLOBALS['aAdminPageFramework']['bEnqueuedSortableFieldScript'] = true;
			}
			if ( ! isset( $GLOBALS['aAdminPageFramework']['bEnqueuedRegisterCallbackScript'] ) ) {
				add_action( 'admin_footer', array( $this, '_replyToAddRegisterCallbackjQueryPlugin' ) );
				$GLOBALS['aAdminPageFramework']['bEnqueuedRegisterCallbackScript'] = true;
			}
			
		}
	/**
	 * Returns the field name for the input tag name attribute.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Dropped the section key. Deprecated the 'name' field key to override the name attribute since the new 'attribute' key supports the functionality.
	 */
	private function _getInputFieldName( $aField=null, $sKey='' ) {
		
		$sKey = ( string ) $sKey;	// this is important as 0 value may have been interpreted as false.
		$aField = isset( $aField ) ? $aField : $this->aField;
		return ( isset( $aField['option_key'] ) // the meta box class does not use the option key
				? "{$aField['option_key']}[{$aField['field_id']}]"
				: $aField['field_id']
			) 
			. ( $sKey !== '0' && empty( $sKey )	// $sKey can be 0 (zero) which yields false
				? ''
				: "[{$sKey}]"
			);
	}
	
	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 * 
	 * @remark			Used by the import and submit field types.
	 * @since			2.0.0
	 * @since			2.1.5			Made the parameter mandatory. Changed the scope to protected from private. Moved from AdminPageFramework_InputField.
	 * @since			3.0.0			Moved from the submit field type class.
	 */ 
	protected function _getFlatInputFieldName( &$aField, $sKey='' ) {	
		
		$sKey = ( string ) $sKey;	// this is important as 0 value may have been interpreted as false.
		return ( isset( $aField['option_key'] ) // the meta box class does not use the option key
				? "{$aField['option_key']}|{$aField['field_id']}"
				: $aField['field_id'] 
			)
			. ( $sKey !== '0' && empty( $sKey )	// $sKey can be 0 (zero) which yields false
				? ""
				: "|{$sKey}"
			);
	}
	
	
	/**
	 * Returns the stored field value.
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Removed the check of the 'value' and 'default' keys.
	 */
	private function _getInputFieldValue( &$aField, $aOptions ) {	

		// Check if a previously saved option value exists or not. Regular setting pages and page meta boxes will be applied here.
		switch( $aField['_field_type'] ) {
			default:
			case 'page':
			case 'page_meta_box':
			case 'taxonomy':
				return isset( $aOptions[ $aField['field_id'] ] )
					? $aOptions[ $aField['field_id'] ]
					: '';	
			case 'post_meta_box':
				return ( isset( $_GET['action'], $_GET['post'] ) ) 
					? get_post_meta( $_GET['post'], $aField['field_id'], true )
					: '';		
			
		}
		return '';
						
	}	
		
	private function _getInputTagID( $aField )  {
				
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
		
		$aFieldsOutput = array(); 
		$aExtraOutput = array();
		
		/* 1. Prepend the field error message. */
		$aFieldsOutput[] = isset( $this->aErrors[ $this->aField['field_id'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->aField['error_message']}" . $this->aErrors[ $this->aField['field_id'] ] . "</span><br />"
			: '';		
					
		/* 2. Set new elements */
		$this->aField['tag_id'] = $this->_getInputTagID( $this->aField );
			
		/* 3. Compose fields array for sub-fields	*/
		$aFields = $this->_composeFieldsArray( $this->aField, $this->aOptions );

		/* 4. Get the field output. */
		foreach( $aFields as $sKey => $aField ) {

			/* 4-1. Retrieve the field definition for this type - this process enables to have mixed field types in sub-fields */ 
			$aFieldTypeDefinition = isset( $this->aFieldTypeDefinitions[ $aField['type'] ] )
				? $this->aFieldTypeDefinitions[ $aField['type'] ] 
				: $this->aFieldTypeDefinitions['default'];
				
			/* 4-2. Set some new elements */ 
			$aField['_index'] = $sKey;
			$aField['input_id'] = "{$aField['field_id']}_{$sKey}";
			$aField['field_name']	= $this->_getInputFieldName( $this->aField, $aField['_is_multiple_fields'] ? $sKey : '' );	
			$aField['_field_name_flat']	= $this->_getFlatInputFieldName( $this->aField, $aField['_is_multiple_fields'] ? $sKey : '' );	// used for submit, export, import field types			
			$aField['_field_container_id'] = "field-{$aField['input_id']}";	// used in the attribute below plus it is also used in the sample custom field type.
			$aField['_fields_container_id'] = "fields-{$this->aField['tag_id']}";
			$aField['_fieldset_container_id'] = "fieldset-{$this->aField['tag_id']}";
			
			$aField['attributes'] = $this->uniteArrays(
				( array ) $aField['attributes'],	// user set values
				array(	// the automatically generated values
					'id' => $aField['input_id'],
					'name' => $aField['field_name'],
					'value' => $aField['value'],
					'type' => $aField['type'],	// text, password, etc.
					'disabled'	=> null,
				),
				( array ) $aFieldTypeDefinition['aDefaultKeys']['attributes']
			);

			/* 4-3. Callback the registered function to output the field */
			$_aFieldAttributes = array(
				'id'	=>	$aField['_field_container_id'],
				'class'	=>	"admin-page-framework-field admin-page-framework-field-{$aField['type']}" 
					. ( $aField['attributes']['disabled'] ? ' disabled' : '' ),
				'data-type'	=>	"{$aField['type']}",	// this is referred by the repeatable field JavaScript script.
			) + $aField['attributes']['field'];
			$aFieldsOutput[] = is_callable( $aFieldTypeDefinition['hfRenderField'] ) 
				? $aField['before_field']
					. "<div " . $this->generateAttributes( $_aFieldAttributes ) . ">"
						. call_user_func_array(
							$aFieldTypeDefinition['hfRenderField'],
							array( $aField )
						)
						. ( ( $sDelimiter = $aField['delimiter'] )
							? "<div " . $this->generateAttributes( array(
									'class'	=>	'delimiter',
									'id'	=>	"delimiter-{$aField['input_id']}",
									'style'	=>	$this->isLastElement( $aFields, $sKey ) ? "display:none;" : "",
								) ) . ">{$sDelimiter}</div>"

							: ""
						)
					. "</div>"
					. $aField['after_field']
				: "";

		}
				
		/* 5. Add the description */
		$aExtraOutput[] = ( isset( $this->aField['description'] ) && trim( $this->aField['description'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->aField['description']}</span></p>"
			: '';
			
		/* 6. Add the repeater script */
		$aExtraOutput[] = $this->aField['repeatable']
			? $this->_getRepeaterFieldEnablerScript( 'fields-' . $this->aField['tag_id'], count( $aFields ), $this->aField['repeatable'] )
			: '';

		/* 7. Add the sortable script */
		$aExtraOutput[] = $this->aField['sortable'] && ( count( $aFields ) > 1 || $this->aField['repeatable'] )
			? $this->_getSortableFieldEnablerScript( 'fields-' . $this->aField['tag_id'] )
			: '';		
				
		/* 8. Return the entire output */
		$_aFieldsSetAttributes = array(
			'id'	=> 'fieldset-' . $this->aField['tag_id'],
			'class'	=> 'admin-page-framework-fieldset',
			'data-field_id'	=>	$this->aField['tag_id'],
		) + $this->aField['attributes']['fieldset'];
		$_aFieldsContainerAttributes = array(
			'id'	=> 'fields-' . $this->aField['tag_id'],
			'class'	=> 'admin-page-framework-fields'
				. ( $this->aField['repeatable'] ? ' repeatable' : '' )
				. ( $this->aField['sortable'] ? ' sortable' : '' ),
			'data-type'	=> $this->aField['type'],	// this is referred by the sortable field JavaScript script.
		) + $this->aField['attributes']['fields'];
		return 
			"<fieldset " . $this->generateAttributes( $_aFieldsSetAttributes ) . ">"
				. $this->_getTableRowIDSetterScript( $this->aField['tag_id'] )	// this needs to be done before each field output gets rendered.
				. "<div " . $this->generateAttributes( $_aFieldsContainerAttributes ) . ">"
					. $this->aField['before_fields'] 
					. implode( PHP_EOL, $aFieldsOutput )
					. $this->aField['after_fields']
				. "</div>"
				. implode( PHP_EOL, $aExtraOutput )
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
AdminPageFramework_Debug::logArray( $vSavedValue );
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
			if ( $aField['repeatable'] ) 
				foreach( ( array ) $vSavedValue as $iIndex => $vValue ) {
					if ( $iIndex == 0 ) continue;
					$aSubFields[ $iIndex - 1 ] = isset( $aSubFields[ $iIndex - 1 ] ) && is_array( $aSubFields[ $iIndex - 1 ] ) 
						? $aSubFields[ $iIndex - 1 ] 
						: array();			
				}
			
			/* Put the initial field and the sub-fields together in one array */
			foreach( $aSubFields as &$aSubField ) {
				
				/* Before merging recursively, evacuate the label element which should not be merged */
				$aLabel = isset( $aSubField['label'] ) 
					? $aSubField['label']
					: ( isset( $aFirstField['label'] )
						 ? $aFirstField['label'] 
						 : null
					);
				
				/* Do recursive array merging */
				$aSubField = $this->uniteArrays( $aSubField, $aFirstField );	// the 'attributes' array of some field types have more than one dimensions. // $aSubField = $aSubField + $aFirstField;
				
				/* Restore the label elemnet */
				$aSubField['label']	= $aLabel;
				
			}
			$aFields = array_merge( array( $aFirstField ), $aSubFields );
					
			/* Set the saved values */		
			if ( count( $aSubFields ) > 0 || $aField['repeatable'] || $aField['sortable'] ) {	// means the elements are saved in an array.
				foreach( $aFields as $iIndex => &$aThisField ) {
					$aThisField['_saved_value'] = isset( $vSavedValue[ $iIndex ] ) ? $vSavedValue[ $iIndex ] : null;
					$aThisField['_is_multiple_fields'] = true;
				}
			} else {
				$aFields[ 0 ]['_saved_value'] = $vSavedValue;
				$aFields[ 0 ]['_is_multiple_fields'] = false;
			} 

			/* Determine the value */
			unset( $aThisField );	// PHP requires this for a previously used variable as reference.
			foreach( $aFields as &$aThisField ) {
				$aThisField['_is_value_set_by_user'] = isset( $aThisField['value'] );
				$aThisField['value'] = isset( $aThisField['value'] ) 
					? $aThisField['value'] 
					: ( isset( $aThisField['_saved_value'] ) 
						? $aThisField['_saved_value']
						: ( isset( $aThisField['default'] )
							? $aThisField['default']
							: null
						)
					);					
			}

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
			$this->_bIsMetaBox = $bTrueOrFalse;
			
		return $this->_bIsMetaBox;
		
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
	private function _getRepeaterFieldEnablerScript( $sFieldsContainerID, $iFieldCount, $aSettings ) {

		$_sAdd = $this->oMsg->__( 'add' );
		$_sRemove = $this->oMsg->__( 'remove' );
		$_sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$_sSettingsAttributes = $this->_generateDataAttributes( ( array ) $aSettings );
		$_sButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons' {$_sSettingsAttributes} >"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$_sAdd}' data-id='{$sFieldsContainerID}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sFieldsContainerID}'>-</a>"
			. "</div>";
		$aJSArray = json_encode( $aSettings );
		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
					nodePositionIndicators = jQuery( '#{$sFieldsContainerID} .admin-page-framework-field .repeatable-field-buttons' );
					if ( nodePositionIndicators.length > 0 ) {	/* If the position of inserting the buttons is specified in the field type definition, replace the pointer element with the created output */
						nodePositionIndicators.replaceWith( \"{$_sButtons}\" );						
					} else {	/* Otherwise, insert the button element at the beginning of the field tag */
						jQuery( '#{$sFieldsContainerID} .admin-page-framework-field' ).prepend( \"{$_sButtons}\" );	// Adds the buttons
					}					
					jQuery( '#{$sFieldsContainerID}' ).updateAPFRepeatableFields( {$aJSArray} );	// Update the fields			
				});
			</script>";
		
	}
		/**
		 * Generates a string of data attributes from the given associative array.
		 * @since			3.0.0
		 */
		private function _generateDataAttributes( array $aArray ) {
			
			$aNewArray = array();
			foreach( $aArray as $sKey => $v ) 
				$aNewArray[ "data-{$sKey}" ] = $v;
				
			return $this->generateAttributes( $aNewArray );
			
		}
	
	/**
	 * Returns the framework's repeatable field jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddRepeatableFieldjQueryPlugin() {
		
		$sCannotAddMore = $this->oMsg->__( 'allowed_maximum_number_of_fields' );
		$sCannotRemoveMore =  $this->oMsg->__( 'allowed_minimum_number_of_fields' );
		
		$sScript = "		
		(function ( $ ) {
		
			$.fn.updateAPFRepeatableFields = function( aSettings ) {
				
				var nodeThis = this;	// it can be from a fields container or a cloned field container.
				var sFieldsContainerID = nodeThis.find( '.repeatable-field-add' ).first().data( 'id' );
				
				/* Store the fields specific options in an array  */
				if( ! $.fn.aAPFRepeatableFieldsOptions ) $.fn.aAPFRepeatableFieldsOptions = [];
				if ( ! $.fn.aAPFRepeatableFieldsOptions.hasOwnProperty( sFieldsContainerID ) ) {		
					$.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ] = $.extend({	
						max: 0,	// These are the defaults.
						min: 0,
						}, aSettings );
				}
				var aOptions = $.fn.aAPFRepeatableFieldsOptions[ sFieldsContainerID ];
				
				/* The Add button behaviour - if the tag id is given, multiple buttons will be selected. 
				 * Otherwise, a field node is given and single button will be selected. */
				$( nodeThis ).find( '.repeatable-field-add' ).click( function() {
					$( this ).addAPFRepeatableField();
					return false;	// will not click after that
				});
				
				/* The Remove button behaviour */
				$( nodeThis ).find( '.repeatable-field-remove' ).click( function() {
					$( this ).removeAPFRepeatableField();
					return false;	// will not click after that
				});		
				
				/* If the number of fields is less than the set minimum value, add fields and vice versa. */
				var sFieldID = nodeThis.find( '.repeatable-field-add' ).first().closest( '.admin-page-framework-field' ).attr( 'id' );
				var nCurrentFieldCount = jQuery( '#' + sFieldsContainerID ).find( '.admin-page-framework-field' ).length;
				if ( aOptions['min'] > 0 && nCurrentFieldCount > 0 ) {
					if ( ( aOptions['min'] - nCurrentFieldCount ) > 0 ) 
						$( '#' + sFieldID ).addAPFRepeatableField( sFieldID );				 
				}
				// if ( aOptions['max'] > 0 && nCurrentFieldCount > 0 ) {
					// if ( nCurrentFieldCount - aOptions['max'] < 0 ) {
						// $( '#' + sFieldID ).removeAPFRepeatableField( sFieldID );
					// }
				// }
				
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

				/* Rebind the click event to the buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields. */
				nodeNewField.updateAPFRepeatableFields();				
				
				/* Increment the names and ids of the next following siblings. */
				nodeFieldContainer.nextAll().each( function() {
					$( this ).incrementIDAttribute( 'id' );
					$( this ).find( 'label' ).incrementIDAttribute( 'for' );
					$( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
					$( this ).find( 'input,textarea,select' ).incrementNameAttribute( 'name' );
				});
				
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
		
		echo "<script type='text/javascript' class='admin-page-framework-repeatable-fields-plugin'>{$sScript}</script>";
	
	}
	
	/**
	 * Adds attribute updater jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddAttributeUpdaterjQueryPlugin() {
		
		$sScript = "
		/**
		 * Attribute increment/decrement jQuery Plugin
		 */		
		(function ( $ ) {
		
			/**
			 * Increments a first found digit with the prefix of underscore in a specified attribute value.
			 */
			$.fn.incrementIDAttribute = function( sAttribute ) {				
				return this.attr( sAttribute, function( iIndex, sValue ) {	
					return updateID( iIndex, sValue, 1 );
				}); 
			};
			/**
			 * Increments a first found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.incrementNameAttribute = function( sAttribute ) {				
				return this.attr( sAttribute, function( iIndex, sValue ) {	
					return updateName( iIndex, sValue, 1 );
				}); 
			};
	
			/**
			 * Decrements a first found digit with the prefix of underscore in a specified attribute value.
			 */
			$.fn.decrementIDAttribute = function( sAttribute ) {
				return this.attr( sAttribute, function( iIndex, sValue ) {
					return updateID( iIndex, sValue, -1 );
				}); 
			};			
			/**
			 * Decrements a first found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.decrementNameAttribute = function( sAttribute ) {
				return this.attr( sAttribute, function( iIndex, sValue ) {
					return updateName( iIndex, sValue, -1 );
				}); 
			};				
			
			/* Sets the current index to the ID attribute */
			$.fn.setIndexIDAttribute = function( sAttribute, iIndex ){
				return this.attr( sAttribute, function( i, sValue ) {
					return updateID( iIndex, sValue, 0 );
				});
			};
			/* Sets the current index to the name attribute */
			$.fn.setIndexNameAttribute = function( sAttribute, iIndex ){
				return this.attr( sAttribute, function( i, sValue ) {
					return updateName( iIndex, sValue, 0 );
				});
			};		
			
			/* Local Function Literals */
			var updateID = function( iIndex, sID, bIncrement ) {
				if ( typeof sID === 'undefined' ) return sID;
				return sID.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {
					if ( bIncrement === 1 )
						return '_' + ( Number(n) + 1 );
					else if ( bIncrement === -1 )
						return '_' + ( Number(n) - 1 );
					else 
						return '_' + ( iIndex );
					// return '_' + ( Number(n) + ( bIncrement === 1 ? 1 : -1 ) );
				});
			}
			var updateName = function( iIndex, sName, bIncrement ) {
				if ( typeof sName === 'undefined' ) return sName;
				return sName.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {	
					if ( bIncrement === 1 )
						return '[' + ( Number(n) + 1 );
					else if ( bIncrement === -1 )
						return '[' + ( Number(n) - 1 );
					else 
						return '[' + ( iIndex );
					// return '[' + ( Number(n) + ( bIncrement === 1 ? 1 : -1 ) );
				});
			}
				
		}( jQuery ));";
		
		echo "<script type='text/javascript' class='admin-page-framework-attribute-updater'>{$sScript}</script>";
		
	}
	
	/**
	 * Returns the JavaScript script that adds the methods to jQuery object that enables for the user to register framework specific callback methods.
	 * @since			3.0.0
	 */
	public function _replyToAddRegisterCallbackjQueryPlugin() {
		
		$sScript = "
			(function ( $ ) {
				
				// The method that gets triggered when a repeatable field add button is pressed.
				$.fn.callBackAddRepeatableField = function( sFieldType, sID ) {
					var nodeThis = this;
					if ( ! $.fn.aAPFAddRepeatableFieldCallbacks ) $.fn.aAPFAddRepeatableFieldCallbacks = [];
					$.fn.aAPFAddRepeatableFieldCallbacks.forEach( function( hfCallback ) {
						if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID );
					});
				};
				
				// The method that gets triggered when a repeatable field remove button is pressed.
				$.fn.callBackRemoveRepeatableField = function( sFieldType, sID ) {
					var nodeThis = this;
					if ( ! $.fn.aAPFRemoveRepeatableFieldCallbacks ) $.fn.aAPFRemoveRepeatableFieldCallbacks = [];
					$.fn.aAPFRemoveRepeatableFieldCallbacks.forEach( function( hfCallback ) {
						if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID );
					});
				};

				// The method that gets triggered when a sortable field is dropped and the sort event occurred
				$.fn.callBackSortedFields = function( sFieldType, sID ) {
					var nodeThis = this;
					if ( ! $.fn.aAPFSortedFieldsCallbacks ) $.fn.aAPFSortedFieldsCallbacks = [];
					$.fn.aAPFSortedFieldsCallbacks.forEach( function( hfCallback ) {
						if ( jQuery.isFunction( hfCallback ) ) hfCallback( nodeThis, sFieldType, sID );
					});
				};
				
				// The method that registers callbacks. This will be called in field type definition class.
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
			
		echo "<script type='text/javascript' class='admin-page-framework-register-callback'>{$sScript}</script>";

	}
	
	/**
	 * Adds jQuery utility plugins.
	 * @since				3.0.0
	 */
	public function _replyToAddUtilityPlugins() {
		
		echo "<script type='text/javascript' class='admin-page-framework-utility-plugins'>
			(function($) {
				$.fn.reverse = [].reverse;
			
				$.fn.formatPrintText = function() {
					var aArgs = arguments;
					return aArgs[ 0 ].replace( /{(\d+)}/g, function( match, number ) {
						return typeof aArgs[ parseInt( number ) + 1 ] != 'undefined'
							? aArgs[ parseInt( number ) + 1 ]
							: match
					;});
				};
			})(jQuery);		
		</script>";
		
	}
	
	/**
	 * Returns the sortable JavaScript script to be loaded in the head tag of the created admin pages.
	 * @since			3.0.0
	 * @access			public	
	 * @internal
	 * @see				https://github.com/farhadi/
	 */
	public function _replyToAddSortableFieldPlugin() {
		
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		/**
		 * HTML5 Sortable jQuery Plugin
		 * http://farhadi.ir/projects/html5sortable
		 * 
		 * Copyright 2012, Ali Farhadi
		 * Released under the MIT license.
		 */
		echo "<script type='text/javascript' class='admin-page-framework-sortable-field-plugin'>
			(function($) {
			var dragging, placeholders = $();
			$.fn.sortable = function(options) {
				var method = String(options);
				options = $.extend({
					connectWith: false
				}, options);
				return this.each(function() {
					if (/^enable|disable|destroy$/.test(method)) {
						var items = $(this).children($(this).data('items')).attr('draggable', method == 'enable');
						if (method == 'destroy') {
							items.add(this).removeData('connectWith items')
								.off('dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s');
						}
						return;
					}
					var isHandle, index, items = $(this).children(options.items);
					var placeholder = $('<' + (/^ul|ol$/i.test(this.tagName) ? 'li' : 'div') + ' class=\"sortable-placeholder\">');
					items.find(options.handle).mousedown(function() {
						isHandle = true;
					}).mouseup(function() {
						isHandle = false;
					});
					$(this).data('items', options.items)
					placeholders = placeholders.add(placeholder);
					if (options.connectWith) {
						$(options.connectWith).add(this).data('connectWith', options.connectWith);
					}
					items.attr('draggable', 'true').on('dragstart.h5s', function(e) {
						if (options.handle && !isHandle) {
							return false;
						}
						isHandle = false;
						var dt = e.originalEvent.dataTransfer;
						dt.effectAllowed = 'move';
						dt.setData('Text', 'dummy');
						index = (dragging = $(this)).addClass('sortable-dragging').index();
					}).on('dragend.h5s', function() {
						dragging.removeClass('sortable-dragging').show();
						placeholders.detach();
						if (index != dragging.index()) {
							items.parent().trigger('sortupdate', {item: dragging});
						}
						dragging = null;
					}).not('a[href], img').on('selectstart.h5s', function() {
						this.dragDrop && this.dragDrop();
						return false;
					}).end().add([this, placeholder]).on('dragover.h5s dragenter.h5s drop.h5s', function(e) {
						if (!items.is(dragging) && options.connectWith !== $(dragging).parent().data('connectWith')) {
							return true;
						}
						if (e.type == 'drop') {
							e.stopPropagation();
							placeholders.filter(':visible').after(dragging);
							return false;
						}
						e.preventDefault();
						e.originalEvent.dataTransfer.dropEffect = 'move';
						if (items.is(this)) {
							if (options.forcePlaceholderSize) {
								placeholder.height(dragging.outerHeight());
							}
							dragging.hide();
							$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
							placeholders.not(placeholder).detach();
						} else if (!placeholders.is(this) && !$(this).children(options.items).length) {
							placeholders.detach();
							$(this).append(placeholder);
						}
						return false;
					});
				});
			};
			})(jQuery);
		</script>";
			
	}
	
	private function _getSortableFieldEnablerScript( $strFieldsContainerID ) {
		
		return 
			"<script type='text/javascript' class='admin-page-framework-sortable-field-enabler-script'>
				jQuery( document ).ready( function() {

					jQuery( '#{$strFieldsContainerID}.sortable' ).sortable(
						{	items: '> div:not( .disabled )', }	// the options for the sortable plugin
					).bind( 'sortupdate', function() {
						
						/* Rename the ids and names */
						var nodeFields = jQuery( this ).children( 'div' );
						var iCount = 1;
						var iMaxCount = nodeFields.length;

						jQuery( jQuery( this ).children( 'div' ).reverse() ).each( function() {	// reverse is needed for radio buttons since they loose the selections when updating the IDs

							var iIndex = ( iMaxCount - iCount );
							jQuery( this ).setIndexIDAttribute( 'id', iIndex );
							jQuery( this ).find( 'label' ).setIndexIDAttribute( 'for', iIndex );
							jQuery( this ).find( 'input,textarea,select' ).setIndexIDAttribute( 'id', iIndex );
							jQuery( this ).find( 'input,textarea,select' ).setIndexNameAttribute( 'name', iIndex );

							/* Radio buttons loose their selections when IDs and names are updated, so reassign them */
							jQuery( this ).find( 'input[type=radio]' ).each( function() {	
								var sAttr = jQuery( this ).prop( 'checked' );
								if ( typeof sAttr !== 'undefined' && sAttr !== false) 
									jQuery( this ).attr( 'checked', 'Checked' );
							});
								
							iCount++;
						});
						
						/* It seems radio buttons need to be taken cared of again. Otherwise, the checked items will be gone. */
						jQuery( this ).find( 'input[type=radio][checked=checked]' ).attr( 'checked', 'Checked' );	
						
						/* Callback the registered functions */
						jQuery( this ).callBackSortedFields( jQuery( this ).data( 'type' ), jQuery( this ).attr( 'id' ) );
						
					}); 		
					
				});
			</script>";
	}
	
	/**
	 * Sets ids to the table rows containing form fields.
	 * @since			3.0.0
	 */
	private function _getTableRowIDSetterScript( $sTagID ) {
		return "<script type='text/javascript' class='admin-page-framework-table-row-id-setter-script'>
			jQuery( '#fieldset-{$sTagID}' ).closest( 'tr' ).attr( 'id', 'fieldrow-{$sTagID}' );
		</script>";
	}
	
}
endif;