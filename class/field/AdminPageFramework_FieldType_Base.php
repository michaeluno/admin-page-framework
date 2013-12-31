<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_Utility {
	
	/**
	 * Defines the slugs used for this field type.
	 * This should be overridden in the extended class.
	 * @access			public
	 */
	public $aFieldTypeSlugs = array( 'default' );
	
	/**
	 * Defines the default key-values of the extended field type. 
	 * This should be overridden in the extended class.
	 */
	protected $aDefaultKeys = array();
	
	/**
	 * Defines the default key-values of all field types.
	 */
	protected static $_aDefaultKeys = array(
		'value'					=> null,				// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'default'				=> null,				// ( array or string )
		'is_repeatable'			=> false,
		// 'class_attribute'		=> '',					// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'label'					=> '',					// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'delimiter'				=> '',
		// 'is_disabled'				=> false,				// ( array or boolean ) This value indicates whether the set field is disabled or not. 
		// 'is_read_only'				=> false,				// ( array or boolean ) sets the readonly attribute to text and textarea input fields.
		'before_input_tag'		=> '',
		'after_input_tag'		=> '',				
		'label_min_width'		=> 140,
		
		/* Mandatory keys */
		'field_id' => null,		
		
		/* For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings. */
		'page_slug' => null,
		'section_id' => null,
		'before_field' => null,
		'after_field' => null,	
		
		'attributes'			=> array(
			'disabled'			=> '',	// set 'Disabled' to make it disabled
			'class'				=> '',
		),
	);	
	
	protected $oMsg;
	
	function __construct( $sClassName, $asFieldTypeSlug=null, $oMsg=null, $bAutoRegister=true ) {
			
		$this->aFieldTypeSlugs = empty( $asFieldTypeSlug ) ? $this->aFieldTypeSlugs : ( array ) $asFieldTypeSlug;
		$this->sClassName = $sClassName;
		$this->oMsg	= $oMsg ? $oMsg : AdminPageFramework_Message::instantiate();
		
		// This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
		if ( $bAutoRegister )
			add_filter( "field_types_{$sClassName}", array( $this, 'replyToRegisterInputFieldType' ) );
	
	}	
	
	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$sClassName} filter.
	 * @since			2.1.5
	 */
	public function replyToRegisterInputFieldType( $aFieldDefinitions ) {
		
		foreach ( $this->aFieldTypeSlugs as $sFieldTypeSlug )
			$aFieldDefinitions[ $sFieldTypeSlug ] = $this->getDefinitionArray( $sFieldTypeSlug );

		return $aFieldDefinitions;		

	}
	
	/**
	 * Returns the field type definition array.
	 * 
	 * @remark			The scope is public since AdminPageFramework_FieldType class allows the user to use this method.
	 * @since			2.1.5
	 * @since			3.0.0			Added the $sFieldTypeSlug parameter.
	 */
	public function getDefinitionArray( $sFieldTypeSlug='' ) {
		
		return array(
			'sFieldTypeSlug'	=> $sFieldTypeSlug,
			'aFieldTypeSlugs'	=> $this->aFieldTypeSlugs,
			'hfRenderField' => array( $this, "replyToGetField" ),
			'hfGetScripts' => array( $this, "replyToGetScripts" ),
			'hfGetStyles' => array( $this, "replyToGetStyles" ),
			'hfGetIEStyles' => array( $this, "replyToGetInputIEStyles" ),
			'hfFieldLoader' => array( $this, "replyToFieldLoader" ),
			'aEnqueueScripts' => $this->getEnqueuingScripts(),	// urls of the scripts
			'aEnqueueStyles' => $this->getEnqueuingStyles(),	// urls of the styles
			'aDefaultKeys' => $this->uniteArrays( $this->aDefaultKeys, self::$_aDefaultKeys ), 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function replytToGetInputField() { return ''; }	// should return the field output
	public function replyToGetScripts() { return ''; }	// should return the script
	public function replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function replyToGetStyles() { return ''; }	// should return the style
	public function replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	// protected function getDefaultKeys() { 
		// return $this->uniteArrays( $this->aDefaultKeys, self::$_aDefaultKeys );
	// }
	
	
}
endif;