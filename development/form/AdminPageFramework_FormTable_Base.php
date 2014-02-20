<?php
if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) :
/**
 * The base class of the form table class that provides methods to render setting sections and fields.
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable_Base extends AdminPageFramework_WPUtility {
	
	public function __construct( $oMsg ) {
		
		$this->oMsg = $oMsg ? $oMsg: AdminPageFramework_Message::instantiate( '' );
		
	}
			
	/*
	* Scripts etc.
	*/ 

	/**
	 * Returns the framework's repeatable field jQuery plugin.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	public function _replyToAddRepeatableSectionjQueryPlugin() {
		
		static $bIsCalled = false;	// the static variable value will take effect even in other instances of the same class.
		
		if ( $bIsCalled ) return;
		$bIsCalled = true;
		echo "<script type='text/javascript' class='admin-page-framework-repeatable-sections-plugin'>"
				. AdminPageFramework_Script_RepeatableSection::getjQueryPlugin( $this->oMsg->__( 'allowed_maximum_number_of_sections' ), $this->oMsg->__( 'allowed_minimum_number_of_sections' ) )
			. "</script>";
	
	}		
	
}
endif;