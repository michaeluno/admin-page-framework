<?php
if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 * @since			3.0.0			Changed the name from AdminPageFramework_CustomFieldType to AdminPageFramework_FieldType.
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_FieldType_Base {

	public function replytToGetInputField( $aField ) { 
		return $this->getField( $aField ); 
	}	// should return the field output
	public function replyToGetScripts() { 
		return $this->getScripts();
	}	// should return the script
	public function replyToGetInputIEStyles() { 
		return $this->getIEStyles(); 
	}	// should return the style for IE
	public function replyToGetStyles() { 
		return $this->getStyles(); 
	}	// should return the style
	public function replyToFieldLoader() {
		$this->load();
	}	// do stuff that should be done when the field type is loaded for the first time.	
	protected function getEnqueuingScripts() { return $this->enqueueScripts(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return $this->enqueueStyles(); }	// should return an array holding the urls of enqueuing items
	
	/*
	 * Available Methods
	 */
	
	/*	
	 * Aliases of the internal callback methods to provide readable names - these methods should be overridden in extended classes defined by the user.
	 */
	protected function load() {}
	protected function getScripts() {} 
	protected function getIEStyles() {}
	protected function getStyles() {}
	protected function getField( $aField ) {}
	
	protected function enqueueScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function enqueueStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	
}
endif;