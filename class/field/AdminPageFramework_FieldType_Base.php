<?php
if ( ! class_exists( 'AdminPageFramework_FieldType_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Field
 * @since			2.1.5
 */
abstract class AdminPageFramework_FieldType_Base extends AdminPageFramework_WPUtility {
	
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
		'value'	=>	null,	// ( array or string ) this suppresses the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'default'	=>	null,	// ( array or string )
		'is_repeatable'	=>	false,
		'label'	=>	'',	// ( string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'delimiter'	=>	'',
		'before_input'	=>	'',
		'after_input'	=>	'',				
		'before_label'	=>	null,
		'after_label'	=>	null,	
		'before_field'	=>	null,
		'after_field'	=>	null,
		'label_min_width'	=> 140,	// in pixel
		
		/* Mandatory keys */
		'field_id' => null,		
		
		/* For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings. */
		'page_slug' => null,
		'section_id' => null,
		'before_fields' => null,
		'after_fields' => null,	
		
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
			'hfRenderField' => array( $this, "_replyToGetField" ),
			'hfGetScripts' => array( $this, "_replyToGetScripts" ),
			'hfGetStyles' => array( $this, "_replyToGetStyles" ),
			'hfGetIEStyles' => array( $this, "_replyToGetInputIEStyles" ),
			'hfFieldLoader' => array( $this, "_replyToFieldLoader" ),
			'aEnqueueScripts' => $this->_replyToGetEnqueuingScripts(),	// urls of the scripts
			'aEnqueueStyles' => $this->_replyToGetEnqueuingStyles(),	// urls of the styles
			'aDefaultKeys' => $this->uniteArrays( $this->aDefaultKeys, self::$_aDefaultKeys ), 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function _replyToGetField( $aField ) { return ''; }	// should return the field output
	public function _replyToGetScripts() { return ''; }	// should return the script
	public function _replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function _replyToGetStyles() { return ''; }	// should return the style
	public function _replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	
	/**
	 * 
	 * return			array			e.g. each element can hold a sting of the source url: array( 'http://..../my_script.js', 'http://..../my_script2.js' )
	 * Optionally, an option array can be passed to specify dependencies etc.
	 * array( array( 'src' => 'http://...my_script1.js', 'dependencies' => array( 'jquery' ) ), 'http://.../my_script2.js' )
	 */
	protected function _replyToGetEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	
	/**
	 * return			array			e.g. each element can hold a sting of the source url: array( 'http://..../my_style.css', 'http://..../my_style2.css' )
	 * Optionally, an option array can be passed to specify dependencies etc.
	 * array( array( 'src' => 'http://...my_style1.css', 'dependencies' => array( 'jquery' ) ), 'http://.../my_style2.css' )
	 */
	protected function _replyToGetEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	
	/*
	 * Shared methods
	 */
	/**
	 * Returns the element value of the given field element.
	 * 
	 * When there are multiple input/select tags in one field such as for the radio and checkbox input type, 
	 * the framework user can specify the key to apply the element value. In this case, this method will be used.
	 * 
	 * @since			3.0.0
	 */
	protected function getFieldElementByKey( $asElement, $sKey, $asDefault='' ) {
		
		if ( ! is_array( $asElement ) )
			return $asElement;
				
		$aElements = &$asElement;	// it is an array
		return isset( $aElements[ $sKey ] )
			? $aElements[ $sKey ]
			: $asDefault;
		
	}	
}
endif;