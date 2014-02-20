<?php
if ( ! class_exists( 'AdminPageFramework_FormField_Base' ) ) :
/**
 * The base class of the form input field class that provides methods for rendering form input fields.
 * 
 * This class mainly handles JavaScript scripts and the constructor setting class properties.
 * 
 * @since			3.0.0			Separated the methods that defines field types to different classes.
 * @extends			AdminPageFramework_WPUtility
 * @package			AdminPageFramework
 * @subpackage		Form
 * @internal
 */
class AdminPageFramework_FormField_Base extends AdminPageFramework_WPUtility {
			
	/**
	 * 
	 * @remark			The third parameter should not be by reference as an expression will be passed.
	 */
	public function __construct( &$aField, &$aOptions, $aErrors, &$aFieldTypeDefinitions, &$oMsg ) {

		/* 1. Set up the properties that will be accessed later in the methods. */
		$aFieldTypeDefinition = isset( $aFieldTypeDefinitions[ $aField['type'] ] ) ? $aFieldTypeDefinitions[ $aField['type'] ] : $aFieldTypeDefinitions['default'];
		
		/* 
		 * 1-1. Set up the 'attributes' array - the 'attributes' element is dealt separately as it contains some overlapping elements with the regular elements such as 'value'.
		 * There are required keys in the attributes array: 'fieldrow', 'fieldset', 'fields', and 'field'; these should not be removed here.
		 */
		$aFieldTypeDefinition['aDefaultKeys']['attributes'] = array(	
			'fieldrow'	=>	$aFieldTypeDefinition['aDefaultKeys']['attributes']['fieldrow'],
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
			
			// In PHP, static variables are alive in newly instantiated objects. So they can serve as a global flag.
			static $_bIsLoadedUtility, $_bIsLoadedRepeatable, $_bIsLoadedSortable, $_bIsLoadedRegisterCallback;
			
			// Global variable
			// $GLOBALS['aAdminPageFramework']['aFieldFlags'] = isset( $GLOBALS['aAdminPageFramework']['aFieldFlags'] )
				// ? $GLOBALS['aAdminPageFramework']['aFieldFlags']
				// : array();
			
			if ( ! $_bIsLoadedUtility ) {
				add_action( 'admin_footer', array( $this, '_replyToAddUtilityPlugins' ) );
				add_action( 'admin_footer', array( $this, '_replyToAddAttributeUpdaterjQueryPlugin' ) );
				$_bIsLoadedUtility = true;
			}
			if ( ! $_bIsLoadedRepeatable ) {
				add_action( 'admin_footer', array( $this, '_replyToAddRepeatableFieldjQueryPlugin' ) );
				$_bIsLoadedRepeatable = true;
			}
			if ( ! $_bIsLoadedSortable ) {
				add_action( 'admin_footer', array( $this, '_replyToAddSortableFieldPlugin' ) );
				$_bIsLoadedSortable = true;
			}
			if ( ! $_bIsLoadedRegisterCallback ) {
				add_action( 'admin_footer', array( $this, '_replyToAddRegisterCallbackjQueryPlugin' ) );
				$_bIsLoadedRegisterCallback = true;
			}
			
		}
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	protected function _getRepeaterFieldEnablerScript( $sFieldsContainerID, $iFieldCount, $aSettings ) {

		$_sAdd = $this->oMsg->__( 'add' );
		$_sRemove = $this->oMsg->__( 'remove' );
		$_sVisibility = $iFieldCount <= 1 ? " style='display:none;'" : "";
		$_sSettingsAttributes = $this->generateDataAttributes( ( array ) $aSettings );
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
	 * Returns the sortable fields script.
	 * 
	 * @since			3.0.0
	 */	
	protected function _getSortableFieldEnablerScript( $strFieldsContainerID ) {
		
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
	 * Returns the framework's repeatable field jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddRepeatableFieldjQueryPlugin() {
		
		echo "<script type='text/javascript' class='admin-page-framework-repeatable-fields-plugin'>"
				. AdminPageFramework_Script_RepeatableField::getjQueryPlugin( $this->oMsg->__( 'allowed_maximum_number_of_fields' ), $this->oMsg->__( 'allowed_minimum_number_of_fields' ) )
			. "</script>";
	
	}
	
	/**
	 * Adds attribute updater jQuery plugin.
	 * @since			3.0.0
	 */
	public function _replyToAddAttributeUpdaterjQueryPlugin() {
		
		echo "<script type='text/javascript' class='admin-page-framework-attribute-updater'>"
				. AdminPageFramework_Script_AttributeUpdator::getjQueryPlugin()
			. "</script>";
		
	}
	
	/**
	 * Returns the JavaScript script that adds the methods to jQuery object that enables for the user to register framework specific callback methods.
	 * @since			3.0.0
	 */
	public function _replyToAddRegisterCallbackjQueryPlugin() {
				
		echo "<script type='text/javascript' class='admin-page-framework-register-callback'>"
				. AdminPageFramework_Script_RegisterCallback::getjQueryPlugin()
			. "</script>";

	}
	
	/**
	 * Adds Admin Page Framework's jQuery utility plugins.
	 * @since				3.0.0
	 */
	public function _replyToAddUtilityPlugins() {
		
		echo "<script type='text/javascript' class='admin-page-framework-utility-plugins'>"
				. AdminPageFramework_Script_Utility::getjQueryPlugin()
			. "</script>";
		
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
		echo "<script type='text/javascript' class='admin-page-framework-sortable-field-plugin'>"
				. AdminPageFramework_Script_Sortable::getjQueryPlugin()
			. "</script>";
			
	}
	
}
endif;