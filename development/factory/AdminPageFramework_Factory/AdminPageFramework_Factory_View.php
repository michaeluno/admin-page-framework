<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Factory_View' ) ) :
/**
 * Provides methods for views.
 * 
 * @abstract
 * @since			3.0.4
 * @subpackage		Factory
 * @internal
 */
abstract class AdminPageFramework_Factory_View extends AdminPageFramework_Factory_Model {
	
	function __construct( $oProp ) {
		
		parent::__construct( $oProp );

		if ( $this->_isInThePage() && 'admin-ajax.php' != $this->oProp->sPageNow ) {
				
			add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
			
		}
		
	}		
	
	/**
	 * Displays stored setting notification messages.
	 * 
	 * @since			3.0.4
	 */
	public function _replyToPrintSettingNotice() {
		
		// Only do this per a page load. PHP static variables will remain in different instantiated objects.
		static $_fIsLoaded;
		
		if ( $_fIsLoaded ) return;
		$_fIsLoaded = true;
		
		$_aNotices = get_transient( 'AdminPageFramework_Notices' );
		if ( false === $_aNotices )	return;

		delete_transient( 'AdminPageFramework_Notices' );
		
		// By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
		if ( isset( $_GET['settings-notice'] ) && ! $_GET['settings-notice'] ) return;
		
		// Display the settings notices.
		$_aPeventDuplicates = array();
		foreach ( ( array ) $_aNotices as $__aNotice ) {
			if ( ! isset( $__aNotice['aAttributes'], $__aNotice['sMessage'] ) || ! is_array( $__aNotice ) ) {
				continue;
			}
			$_sNotificationKey = md5( serialize( $__aNotice ) );
			if ( isset( $_aPeventDuplicates[ $_sNotificationKey ] ) ) {
				continue;
			}
			$_aPeventDuplicates[ $_sNotificationKey ] = true;
			echo "<div " . $this->oUtil->generateAttributes( $__aNotice['aAttributes'] ). "><p>" . $__aNotice['sMessage'] . "</p></div>";
			
		}
		
	}
	
	
	/**
	 * Returns the field output from the given field definition array.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToGetFieldOutput( $aField ) {

		$_oField = new AdminPageFramework_FormField( $aField, $this->oProp->aOptions, $this->_getFieldErrors(), $this->oProp->aFieldTypeDefinitions, $this->oMsg );	// currently the error array is not supported for meta-boxes		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 	'field_' . $this->oProp->sClassName . '_' . $aField['field_id'] ),	// field_ + {extended class name} + _ {field id}
			$_oField->_getFieldOutput(),	// field output
			$aField // the field array
		);		
						
	}	
		
	
}
endif;