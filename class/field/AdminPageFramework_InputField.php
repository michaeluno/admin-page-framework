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
		 * */
		$aFieldTypeDefinition['aDefaultKeys']['attributes'] = array(	
			'fieldset'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldset'],
			'fields'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['fields'],
			'field'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['field'],
		);	
		$this->aField = $this->uniteArrays( $aField, $aFieldTypeDefinition['aDefaultKeys'] );
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
				? "{$aField['option_key']}[{$aField['page_slug']}][{$aField['field_id']}]"
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
				? "{$aField['option_key']}|{$aField['page_slug']}|{$aField['field_id']}"
				: $aField['field_id'] 
			)
			. ( $sKey !== '0' && empty( $sKey )	// $sKey can be 0 (zero) which yields false
				? ""
				: "|{$sKey}"
			);
	}
	
	
	/**
	 * 
	 * @since			2.0.0
	 * @since			3.0.0			Removed the check of the 'value' and 'default' keys.
	 */
	private function _getInputFieldValue( &$aField, $aOptions ) {	

		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $aField['page_slug'], $aField['section_id'] ) ) 
			return $this->_getInputFieldValueFromOptionTable( $aField, $aOptions );
		
		// For meta boxes
		if ( isset( $_GET['action'], $_GET['post'] ) ) 
			return $this->_getInputFieldValueFromPostTable( $_GET['post'], $aField );
			
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
				'id'	=>	"field-{$aField['input_id']}",
				'class'	=>	"admin-page-framework-field admin-page-framework-field-{$aField['type']}" 
					. ( $aField['attributes']['disabled'] ? ' disabled' : '' ),
				'data-type'	=>	"{$aField['type']}",
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
		$aExtraOutput[] = $this->aField['is_repeatable']
			? $this->_getRepeaterFieldEnablerScript( 'fields-' . $this->aField['tag_id'], count( $aFields ) )
			: '';

		/* 7. Add the sortable script */
		$aExtraOutput[] = $this->aField['is_sortable'] && ( count( $aFields ) > 1 || $this->aField['is_repeatable'] )
			? $this->_getSortableFieldEnablerScript( 'fields-' . $this->aField['tag_id'] )
			: '';			
		
		/* 8. Return the entire output */
		$_aFieldsSetAttributes = array(
			'id'	=> $this->aField['tag_id'],
			'class'	=> 'admin-page-framework-fieldset',
		) + $this->aField['attributes']['fieldset'];
		$_aFieldsContainerAttributes = array(
			'id'	=> 'fields-' . $this->aField['tag_id'],
			'class'	=> 'admin-page-framework-fields'
				. ( $this->aField['is_repeatable'] ? ' repeatable' : '' )
				. ( $this->aField['is_sortable'] ? ' sortable' : '' ),
		) + $this->aField['attributes']['fields'];
		return $this->_getRepeaterScriptGlobal()
			. "<fieldset " . $this->generateAttributes( $_aFieldsSetAttributes ) . ">"
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
				$aSubField = $this->uniteArrays( $aSubField, $aFirstField );	// the 'attributes' array of some field types have more than one dimensions. // $aSubField = $aSubField + $aFirstField;
			$aFields = array_merge( array( $aFirstField ), $aSubFields );
					
			/* Set the saved values */		
			if ( count( $aSubFields ) > 0 || $aField['is_repeatable'] || $aField['is_sortable'] ) {	// means the elements are saved in an array.
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
	private function _getRepeaterFieldEnablerScript( $sTagID, $iFieldCount ) {

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
					
					nodePositionIndicators = jQuery( '#{$sTagID} .admin-page-framework-field .repeatable-field-buttons' );
					if ( nodePositionIndicators.length > 0 ) {
						
						/* If the position of inserting the buttons is specified in the field type definition, replace the pointer element with the created output */
						nodePositionIndicators.replaceWith( \"{$sButtons}\" );
						
					} else {
						
						/* Otherwise, insert the button element at the beginning of the field tag */
						jQuery( '#{$sTagID} .admin-page-framework-field' ).prepend( \"{$sButtons}\" );	// Adds the buttons
					}
					
					updateAPFRepeatableFields( '{$sTagID}' );	// Update the fields					
				});
			</script>";
		
	}

	/**
	 * Returns the script that will be referred multiple times.
	 * since			2.1.3
	 */
	// public function _replyToAddRepeatableFieldScript() {
	private function _getRepeaterScriptGlobal() {

		if ( isset( $GLOBALS['aAdminPageFramework']['bIsRepeatableScriptCalled'] ) && $GLOBALS['aAdminPageFramework']['bIsRepeatableScriptCalled'] ) return '';
		$GLOBALS['aAdminPageFramework']['bIsRepeatableScriptCalled'] = true;
		return
		"<script type='text/javascript' class='admin-page-framework-repeatable-field-script'>
			jQuery( document ).ready( function() {
												
				/* This function gets triggered when the document becomes ready. 
					The tag id of the fields container is given for multiple fields to deal with at once.
					Otherwise, a node object is given to deal with a singe field.
				*/
				updateAPFRepeatableFields = function( vTagIDOrNode ) {

					var sTagID = ( typeof vTagIDOrNode == 'string' || vTagIDOrNode instanceof String )
						? vTagIDOrNode
						: vTagIDOrNode.closest( '.admin-page-framework-fields' ).attr( 'id' );
										
					/* The Add button behaviour - if the tag id is given, multiple buttons will be selected. 
					 * Otherwise, a field node is given and single button will be selected. */
					var nodeAddButtons = ( typeof vTagIDOrNode == 'string' || vTagIDOrNode instanceof String )
						? jQuery( '#' + sTagID + ' .repeatable-field-add' )
						: jQuery( vTagIDOrNode ).find( '.repeatable-field-add' );
					nodeAddButtons.click( function() {						
						var nodeFieldContainer = jQuery( this ).closest( '.admin-page-framework-field' );
						addAPFRepeatableField( nodeFieldContainer.attr( 'id' ) );
						return false;
					});		
					
					/* The Remove button behaviour */
					var nodeRemobeButtons = ( typeof vTagIDOrNode == 'string' || vTagIDOrNode instanceof String )
						? jQuery( '#' + sTagID + ' .repeatable-field-remove' )
						: jQuery( vTagIDOrNode.find( '.repeatable-field-remove' ) );
					nodeRemobeButtons.click( function() {
						
						/* Need to remove the element: the field container */
						var nodeFieldContainer = jQuery( this ).closest( '.admin-page-framework-field' );
						var nodeFieldsContainer = jQuery( this ).closest( '.admin-page-framework-fields' );
						
						/* Decrement the names and ids of the next following siblings. */
						nodeFieldContainer.nextAll().each( function() {
							jQuery( this ).decrementIDAttribute( 'id' );
							jQuery( this ).find( 'label' ).decrementIDAttribute( 'for' );
							jQuery( this ).find( 'input,textarea,select' ).decrementIDAttribute( 'id' );
							jQuery( this ).find( 'input,textarea,select' ).decrementNameAttribute( 'name' );																	
						});

						/* Call the registered callback functions */
						nodeFieldContainer.callBackRemoveRepeatableField( nodeFieldContainer.data( 'type' ), nodeFieldContainer.attr( 'id' ) );	
					
						/* Remove the field */
						nodeFieldContainer.remove();
						
						/* Count the remaining Remove buttons and if it is one, disable the visibility of it */
						var nodeRemoveButtons = nodeFieldsContainer.find( '.repeatable-field-remove' );
						var iFieldsCount = nodeRemoveButtons.length;
						if ( iFieldsCount == 1 ) {
							nodeRemoveButtons.css( 'display', 'none' );
						}

						return false;
					});
									
				}
		
				// This function is called from the updateAPFRepeatableFields() and from the media uploader for multiple file selections.
				addAPFRepeatableField = function( sFieldContainerID ) {	

					var nodeFieldContainer = jQuery( '#' + sFieldContainerID );
					var nodeNewField = nodeFieldContainer.clone();	// clone without bind events.
					var nodeFieldsContainer = nodeFieldContainer.closest( '.admin-page-framework-fields' );

					nodeNewField.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );	// empty the value		

					/* Rebind the click event to the buttons */
					updateAPFRepeatableFields( nodeNewField );
					
					/* Add the cloned new field element */
					nodeNewField.insertAfter( nodeFieldContainer );	

					/* Increment the names and ids of the next following siblings. */
					nodeFieldContainer.nextAll().each( function() {
						jQuery( this ).incrementIDAttribute( 'id' );
						jQuery( this ).find( 'label' ).incrementIDAttribute( 'for' );
						jQuery( this ).find( 'input,textarea,select' ).incrementIDAttribute( 'id' );
						jQuery( this ).find( 'input,textarea,select' ).incrementNameAttribute( 'name' );
					});
				
					/* Call the registered callback functions */
					nodeNewField.callBackAddRepeatableField( nodeNewField.data( 'type' ), nodeNewField.attr( 'id' ) );					
					
					/* If more than one fields are created, show the Remove button */
					var nodeRemoveButtons =  nodeFieldsContainer.find( '.repeatable-field-remove' );
					if ( nodeRemoveButtons.length > 1 ) 
						nodeRemoveButtons.show();				
										
					/* Return the newly created element */
					return nodeNewField;
					
				}
			});
		</script>";
	}

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
				return this.attr( sAttribute, function( index, val ) {	
					return updateID( index, val, 1 );
				}); 
			};
			/**
			 * Decrements a first found digit with the prefix of underscore in a specified attribute value.
			 */
			$.fn.decrementIDAttribute = function( sAttribute ) {
				return this.attr( sAttribute, function( index, val ) {
					return updateID( index, val, 0 );
				}); 
			};
			/**
			 * Increments a first found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.incrementNameAttribute = function( sAttribute ) {				
				return this.attr( sAttribute, function( index, val ) {	
					return updateName( index, val, 1 );
				}); 
			};
			/**
			 * Decrements a first found digit enclosed in [] in a specified attribute value.
			 */
			$.fn.decrementNameAttribute = function( sAttribute ) {
				return this.attr( sAttribute, function( index, val ) {
					return updateName( index, val, 0 );
				}); 
			};		
			
			/* Local Function Literals */
			var updateID = function( index, sID, bIncrement ) {
				if ( typeof sID === 'undefined' ) return sID;
				return sID.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
					return '_' + ( Number(n) + ( bIncrement === 1 ? 1 : -1 ) );
				});
			}
			var updateName = function( index, sName, bIncrement ) {
				if ( typeof sName === 'undefined' ) return sName;
				return sName.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
					return '[' + ( Number(n) + ( bIncrement === 1 ? 1 : -1 ) );
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
				
				// The method that registers callbacks. This will be used in field type definition class.
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
							jQuery( this ).attr( 'id', function( index, name ) { return setID( iIndex, name ) } );
							jQuery( this ).find( 'label' ).attr( 'for', function( index, name ){ return setID( iIndex, name ) } );
							jQuery( this ).find( 'input,textarea,select' ).attr( 'id', function( index, name ){ return setID( iIndex, name ) } );
							jQuery( this ).find( 'input,textarea,select' ).attr( 'name', function( index, name ){ return setName( iIndex, name ) } );				

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
						
					}); 
					
					/* Helper Local Function Literals */
					var setID = function( index, name ) {
						
						if ( typeof name === 'undefined' ) return name;
						return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {
							return '_' + index;
						});
						
					}
					var setName = function( index, name ) {
						
						if ( typeof name === 'undefined' ) return name;
						return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {
							return '[' + index;
						});
						
					}				
					
				});
			</script>";
	}
	
}
endif;