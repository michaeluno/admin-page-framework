<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FormTable_Base' ) ) :
/**
 * The base class of the form table class that provides methods to render setting sections and fields.
 * 
 * This base class mainly deals with setting properties in the constructor and internal methods. 
 * 
 * @package			AdminPageFramework
 * @subpackage		Form
 * @since			3.0.0
 * @internal
 */
class AdminPageFramework_FormTable_Base extends AdminPageFramework_WPUtility {
	
	public function __construct( $aFieldTypeDefinitions, $oMsg ) {
		
		$this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
		$this->oMsg = $oMsg ? $oMsg: AdminPageFramework_Message::instantiate( '' );
		
		$this->_loadScripts();
		
	}
		
		/**
		 * Inserts necessary JavaScript scripts for fields.
		 * 
		 * @since			3.0.0
		 */ 
		private function _loadScripts() {
			
			static $_bIsLoadedTabPlugin;
			
			if ( ! $_bIsLoadedTabPlugin ) 
				$_bIsLoadedTabPlugin = add_action( 'admin_footer', array( $this, '_replyToAddTabPlugin' ) );
			
		}
		
	/**
	 * Generates attributes of the field container tag.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	protected function _getAttributes( $aField, $aAttributes=array() ) {

		$_aAttributes = $aAttributes + ( isset( $aField['attributes']['fieldrow'] ) ? $aField['attributes']['fieldrow'] : array() );
		
		if ( $aField['hidden'] )	// Prepend the visibility CSS property.
			$_aAttributes['style'] = 'display:none;' . ( isset( $_aAttributes['style'] ) ? $_aAttributes['style'] : '' );
		
		return $this->generateAttributes( $_aAttributes );
		
	}
	
	/**
	 * Returns the title part of the field output.
	 * 
	 * @since			3.0.0
	 * @internal
	 */
	protected function _getFieldTitle( $aField ) {
		
		return "<label for='{$aField['field_id']}'>"
			. "<a id='{$aField['field_id']}'></a>"
				. "<span title='" . ( strip_tags( isset( $aField['tip'] ) ? $aField['tip'] : $aField['description'] ) ) . "'>"
					. $aField['title'] 
				. "</span>"
			. "</label>";
		
	}

	/**
	 * Merge the given field definition array with the field type default key array that holds default values.
	 * 
	 * This is important for the getFieldRow() method to know if the field should have specific styling or the hidden key is set or not,
	 * which affects the way of rendering the row that contains the field output (by the field output callback).
	 * 
	 * @internal
	 * @since			3.0.0
	 * @remark			The returning merged field definition array does not respect sub-fields so when passing the field definition to the callback,
	 * do not use the array returned from this method but the raw (non-merged) array.
	 */
	protected function _mergeDefault( $aField ) {

		return $this->uniteArrays( 
			$aField, 
			isset( $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] ) 
				? $this->aFieldTypeDefinitions[ $aField['type'] ]['aDefaultKeys'] 
				: array()
		);
		
	}

	
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

	/**
	 * Returns the tab JavaScript script.
	 * 
	 * @since			3.0.0
	 */
	public function _replyToAddTabPlugin() {
		
		echo "<script type='text/javascript' class='admin-page-framework-tab-plugin'>"
				. AdminPageFramework_Script_Tab::getjQueryPlugin()
			. "</script>";
			
	}
	
}
endif;