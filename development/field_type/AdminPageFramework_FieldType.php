<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_FieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * When a framework user implements a custom field type into his/her work, this class may be extended to create a field definition class.
 * 
 * <h3>Steps to Include a Custom Field Type</h3>
 * <ol>
 * 	<li>
 * 		Define a custom field type with a class extending the <em>AdminPageFramework_FieldType</em> class.
 * 		<ol>
 * 			<li>Set the field type slug such as autocomplete with the <em>$aFieldTypeSlugs</em> property.</li>
 * 			<li>Set the default field array definition keys with the <em>$aDefaultKeys</em> property.</li>
 * 			<li>Write additional code in the <em>setUp()</em> method that will be performed when the field type definition is parsed.</li>
 * 			<li>Add scripts and styles with <em>getEnqueuingScripts()</em>, <em>getEnqueuingStyles()</em>, <em>getScripts()</em>, <em>getStyles()</em> etc.</li>
 * 			<li>Compose the output HTML structure with the passed <em>$aField</em> field definition array in the <em>getField()</em> method.</li>
 * 		</ol>
 * 	</li>
 * 	<li>
 * 		Include the definition file and instantiate the class in the script(plugin,theme etc.).
 * 		<code>
 * 		new MyCustomFieldTypeClass( 'MY_CLASS_NAME' );   // pass the PHP class name that extends the framework's class to the first parameter.
 * 		</code>
 * 	</li>
 * 	<li>
 * 		Define fields with the custom field type with the <em>addSettingFields()</em> method in the framework extending class.
 * 		<code>
 *			$this->addSettingFields(
 *				array(  
 *					'field_id'  =>  'my_field_id',
 *					'section_id'    =>  'my_section_id',
 *					'type'  =>  'my_custom_field_type_slug',    // <-- here put the field type slug
 *					'...' => '...'
 *				)
 *			);
 * 		</code>
 * 	</li>
 * </ol>
 * 
 * @abstract
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
	/**#@-*/
    /**
     * @internal
     */	
	protected function _replyToGetEnqueuingScripts() { return $this->getEnqueuingScripts(); }	// should return an array holding the urls of enqueuing items
    /**
     * @internal
     */	
	protected function _replyToGetEnqueuingStyles() { return $this->getEnqueuingStyles(); }	// should return an array holding the urls of enqueuing items
	
	/*
	 * Required Properties
	 */
	/**
	 * Defines the field type slugs used for this field type.
	 * 
	 * The slug is used for the type key in a field definition array.
	 * 
	 * <code>
	 * 	$this->addSettingFields(
	 *		array(
	 *			'section_id'	=>	'...',
	 *			'type'	=>	'my_filed_type_slug',	// <--- THIS PART
	 *			'field_id'	=>	'...',
	 *			'title'		=>	'...',
	 *		)
	 *	);
	 * </code>
	 */	
	public $aFieldTypeSlugs = array();
	
	/**
	 * Defines the default key-values of this field type. 
	 * 
	 * The users will set the values to the defined keys and if not set, the value set in this property array will take effect. The merged array of the user's field definition array and this property array will be passed to the first parameter of the <em>getField()</em> method.
	 * 
	 * <code>
	 *  $this->addSettingFields(
	 *		array(
	 *			'section_id'	=>	'...',	
	 *			'type'	=>	'...',
	 *			'field_id'	=>	'...',
	 *			'my_custom_key' => '...',	// <-- THIS PART
	 *		)
	 *	);
	 * </code>
	 * 
	 * <h4>Example</h4>
	 * <code>
	 * $aDefaultKeys = array(
	 *	 	'my_custom_key' => 'my default value',
	 *	 	'attributes'	=> array(
	 *			'size'	=>	30,
	 *			'maxlength'	=>	400,
	 *		),
	 *	);
	 * </code>
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
	/**
	 * Loads the field type necessary components.
	 * 
	 * This method is triggered when a field definition array that calls this field type is parsed. 
	 */ 	
	protected function setUp() {}
	protected function getScripts() { return ''; } 
	protected function getIEStyles() { return ''; }
	protected function getStyles() { return ''; }
	protected function getField( $aField ) { return ''; }
	
	/**
	 * Returns an array holding the urls of enqueuing scripts.
	 * 
	 * The returning array should consist of all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
	 * 
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>translation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>in_footer</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Examples</h4>
	 * <code>
	 * protected function getEnqueuingScripts() { 
	 *	return array(
	 *		array( 	// if you need to set a dependency, pass as a custom argument array. 
	 *			'src'	=> dirname( __FILE__ ) . '/asset/my_script.js', 	// path or url
	 *			'dependencies'	=> array( 'jquery' ) 
	 *		),
	 *		dirname( __FILE__ ) . '/asset/my_another.js',	// a string value of the target path or url will work as well.
	 *	);
	 * }
	 * </code>
	 */	
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	
	/**
	 * Returns an array holding the urls of enqueuing styles.
	 * 
	 * The returning array should consist of all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
	 * 
	 * <h4>Custom Argument Array</h4>
	 * <ul>
	 * 	<li><strong>src</strong> - ( required, string ) The url or path of the target source file</li>
	 * 	<li><strong>handle_id</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>dependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>version</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>media</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * <h4>Examples</h4>
	 * <code>
	 *	protected function getEnqueuingStyles() { 
	 *		return array(
	 *			dirname( __FILE__ ) . '/asset/my_style.css',
	 *			array(
	 *				'src' => dirname( __FILE__ ) . '/asset/my_style2.css',
	 *				'handle_id' => 'my_style2',
	 *			),
	 *		);
	 *	}			
	 * </code>
	 */	
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	/**#@-*/
	
}
endif;