<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_Model' ) ) :
/**
 * Provides methods for models.
 * 
 * @abstract
 * @since			3.0.4
 * @subpackage		Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_Model extends AdminPageFramework_Factory_Router {
	
	function __construct( $oProp ) {
		
		parent::__construct( $oProp );
		
// TODO: check the field errors and delete the transient
$this->_deleteFieldErrors();
		
	}	
	
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public function _loadDefaultFieldTypeDefinitions() {
		
		static $_aFieldTypeDefinitions = array();	// Stores the default field definitions. Once they are set, it no longer needs to be done.
		
		if ( empty( $_aFieldTypeDefinitions ) ) {
			
			// This class adds filters for the field type definitions so that framework's default field types will be added.
			new AdminPageFramework_FieldTypeRegistration( $_aFieldTypeDefinitions, $this->oProp->sClassName, $this->oMsg );					
			
		} 
				
		$this->oProp->aFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $sFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProp->sClassName,	// 'field_types_' . {extended class name}
			$_aFieldTypeDefinitions
		);				
		
	}	

	/**
	 * Registers the given fields.
	 * 
	 * @remark			$oHelpPane and $oHeadTab need to be set in the extended class.
	 * @since			3.0.0
	 * @internal
	 */
	protected function _registerFields( array $aFields ) {

		foreach( $aFields as $_sSecitonID => $_aFields ) {
			
			$_bIsSubSectionLoaded = false;
			foreach( $_aFields as $_iSubSectionIndexOrFieldID => $_aSubSectionOrField )  {
				
				// if it's a sub-section
				if ( is_numeric( $_iSubSectionIndexOrFieldID ) && is_int( $_iSubSectionIndexOrFieldID + 0 ) ) {	

					if ( $_bIsSubSectionLoaded ) continue;		// no need to repeat the same set of fields
					$_bIsSubSectionLoaded = true;
					foreach( $_aSubSectionOrField as $_aField ) {
						$this->_registerField( $_aField );						
					}
					continue;
				}
					
				$_aField = $_aSubSectionOrField;
				$this->_registerField( $_aField );
			
			}
		}
		
	}
		/**
		 * Registers a field.
		 * 
		 * @since			3.0.4
		 * @internal
		 */
		private function _registerField( array $aField ) {
			
			// Load head tag elements for fields.
			AdminPageFramework_FieldTypeRegistration::_setFieldHeadTagElements( $aField, $this->oProp, $this->oHeadTag );	// Set relevant scripts and styles for the input field.

			// For the contextual help pane,
			if ( $aField['help'] ) {
				$this->oHelpPane->_addHelpTextForFormFields( $aField['title'], $aField['help'], $aField['help_aside'] );									
			}
			
		}	
	

	/**
	 * Retrieves the settings error array set by the user in the validation callback.
	 * 
	 * @since				3.0.4			
	 * @access				private
	 * @internal
	 * 
	 * @param				string			$sID			deprecated
	 * @param				boolean			$bDelete		whether or not the transient should be deleted after retrieving it. 
	 */
	protected function _getFieldErrors( $sID='', $bDelete=true ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		// if ( ! isset( $_GET['settings-updated'] ) ) return null;
//TODO: check whether the page is locded right after the user's submitting the form, and is not, return null. <-- might not be necessary as it's done in the constructor.

//TODO: currently field error transients are set per each class. This increases the number of DB queries as the instances of class increases. To avoid that, implement a metric that deals with those at once, maybe at shutdown.
		// Find the transient.
		$_sTransientKey = md5( $this->oProp->sClassName );

		$_aFieldErrors = get_transient( $_sTransientKey );
		if ( $bDelete ) {
			delete_transient( $_sTransientKey );	
		}
		return $_aFieldErrors;

	}	
	
	/**
	 * Checks whether a validation error is set.
	 * 
	 * @since			3.0.3
	 * @return			mixed			If the error is not set, returns false; otherwise, the stored error array.
	 */
	protected function _isValidationErrors() {

		return get_transient( md5( $this->oProp->sClassName ) );

	}
	
	/**
	 * Deletes the field errors.
	 * 
	 * @since			3.0.4
	 */
	protected function _deleteFieldErrors() {
		delete_transient( md5( $this->oProp->sClassName . '_' . $this->oProp->sClassName ) );
	}
		
		
	/**
	 * Saves the notification array set via the setSettingNotice() method.
	 * 
	 * @remark			This method will be triggered with the 'shutdown' hook.
	 * @since			3.0.4 
	 */
	public function _replyToSaveNotices() {
		
		if ( ! isset( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) return;
		if ( empty( $GLOBALS['aAdminPageFramework']['aNotices'] ) ) return;
			
		set_transient( 'admin_page_framework_notices', $GLOBALS['aAdminPageFramework']['aNotices'] );
		
	}
	
}
endif;