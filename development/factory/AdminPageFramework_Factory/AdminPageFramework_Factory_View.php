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

		if ( $this->_isInThePage() && ! $this->oProp->bIsAdminAjax ) {				
			add_action( 'admin_notices', array( $this, '_replyToPrintSettingNotice' ) );
		}
		
	}		
	
	/**
	 * Stores a flag value indicating whether the setting notice method is called or not.
	 * 
	 * @since			3.1.3
	 */
	static private $_bSettingNoticeLoaded = false;
	
	/**
	 * Displays stored setting notification messages.
	 * 
	 * @since			3.0.4
	 */
	public function _replyToPrintSettingNotice() {
			
		// Ensure this method is called only once per a page load.
		if ( self::$_bSettingNoticeLoaded ) { return; }
		self::$_bSettingNoticeLoaded = true;

		$_aNotices = get_transient( 'AdminPageFramework_Notices' );
		if ( false === $_aNotices )	{ return; }
		delete_transient( 'AdminPageFramework_Notices' );
	
		// By setting false to the 'settings-notice' key, it's possible to disable the notifications set with the framework.
		if ( isset( $_GET['settings-notice'] ) && ! $_GET['settings-notice'] ) { return; }
		
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
			$__aNotice['aAttributes']['class'] = isset( $__aNotice['aAttributes']['class'] )
				? $__aNotice['aAttributes']['class'] . ' admin-page-framework-settings-notice-container'
				: 'admin-page-framework-settings-notice-container';
			echo "<div " . $this->oUtil->generateAttributes( $__aNotice['aAttributes'] ). ">"
					. "<p class='admin-page-framework-settings-notice-message'>" . $__aNotice['sMessage'] . "</p>"
				. "</div>";
			
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