<?php
if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			AdminPageFramework
 * @subpackage		FieldType
 * @since			2.1.5
 * @since			3.0.0			Changed the name from AdminPageFramework_CustomFieldType to AdminPageFramework_FieldType.
 * @remark			The user will extend this class to define their custom field types.
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_FieldType_Base {

	/*
	 *	Convert internal method names for the users to use to be easy to read. 
	 */
    /**#@+
     * @internal
     */
	public function _replyToFieldLoader() { $this->setUp(); }	// do stuff that should be done when the field type is loaded for the first time.	
	public function _replyToGetScripts() { return $this->getScripts(); }	// should return the script
	public function _replyToGetInputIEStyles() { return $this->getIEStyles(); }	// should return the style for IE
	public function _replyToGetStyles() { return $this->getStyles(); }	// should return the style
	public function _replyToGetField( $aField ) {  return $this->getField( $aField ); }	// should return the field output
	protected function _replyToGetEnqueuingScripts() { return $this->getEnqueuingScripts(); }	// should return an array holding the urls of enqueuing items
	protected function _replyToGetEnqueuingStyles() { return $this->getEnqueuingStyles(); }	// should return an array holding the urls of enqueuing items
	/**#@-*/
	
	/*
	 * Required Properties
	 */
	/**
	 * Defines the field type slugs used for this field type.
	 * 
	 * e.g. $aFieldTypeSlugs = array( 'my_field_type' )
	 */
	public $aFieldTypeSlugs = array();
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * e.g. $aDefaultKeys = array(
	 *	 	'attributes'	=> array(
				'size'	=>	30,
				'maxlength'	=>	400,
			),
		)
	 * 
	 * @remark			$_aDefaultKeys holds shared default key-values defined in the base class.
	 */
	protected $aDefaultKeys = array();
	
	/*
	 * Available Methods for Users - these methods should be overridden in extended classes.
	 */
    /**#@+
     * @since			3.0.0
	 * @remark			The user will override this method in their class definition.
     */	
	protected function setUp() {}
	protected function getScripts() { return ''; } 
	protected function getIEStyles() { return ''; }
	protected function getStyles() { return ''; }
	protected function getField( $aField ) { return ''; }
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	/**#@-*/
	
}
endif;