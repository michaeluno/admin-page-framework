<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class for the users to create their custom field types.
 * 
 * When a framework user implements a custom field type into his/her work, this class may be extended to create a field definition class.
 * 
 * @abstract
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       2.1.5
 * @since       3.0.0       Changed the name from `AdminPageFramework_CustomFieldType` to `AdminPageFramework_FieldType`.
 * @remark      The user will extend this class to define their custom field types.
 */
abstract class AdminPageFramework_FieldType extends AdminPageFramework_FieldType_Base {

    /*
     * Convert internal method names for the users to use to be easy to read. 
     */
    /**#@+
     * @internal
     */
    public function _replyToFieldLoader() { $this->setUp(); }                               // do stuff that should be done when the field type is loaded for the first time.    
    public function _replyToGetScripts() { return $this->getScripts(); }                    // should return the script
    public function _replyToGetInputIEStyles() { return $this->getIEStyles(); }             // should return the style for IE
    public function _replyToGetStyles() { return $this->getStyles(); }                      // should return the style
    public function _replyToGetField( $aField ) {  return $this->getField( $aField ); }     // should return the field output
   
    protected function _replyToGetEnqueuingScripts() { return $this->getEnqueuingScripts(); }   // should return an array holding the urls of enqueuing items
    protected function _replyToGetEnqueuingStyles() { return $this->getEnqueuingStyles(); }     // should return an array holding the urls of enqueuing items
    /**#@-*/
    
    /*
     * Required Properties
     */
    /**
     * Defines the field type slugs used for this field type.
     * 
     * The slug is used for the type key in a field definition array.
     * 
     * <code>
     * $this->addSettingFields(
     *      array(
     *          'section_id'    => '...',
     *          'type'          => 'my_filed_type_slug', // <--- THIS PART
     *          'field_id'      => '...',
     *          'title'         => '...',
     *      )
     * );
     * </code>
     * 
     * <h4>Example</h4>
     * <code>
     * public $aFieldTypeSlugs = array( 'my_field_type_slug', 'alternative_field_type_slug' );
     * </code>
     * @access       public      This must be public as accessed from outside.
     */    
    public $aFieldTypeSlugs = array( 'default', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * The users will set the values to the defined keys and if not set, the value set in this property array will take effect. The merged array of the user's field definition array and this property array will be passed to the first parameter of the `getField()` method.
     * 
     * <code>
     *  $this->addSettingFields(
     *      array(
     *          'section_id'         => '...', // built-in key
     *          'type'               => '...', // built-in key
     *          'field_id'           => '...', // built-in key
     *          'my_custom_key'      => 'the default value for this key', // <-- THIS PART
     *          'another_custom)key' => 'the default value for this key', // <-- THIS PART
     *      )
     *  );
     * </code>
     * 
     * <h4>Example</h4>
     * <code>
     * $aDefaultKeys = array(
     *      'my_custom_key' => 'my default value',
     *      'attributes'    => array(
     *          'size'      => 30,
     *          'maxlength' => 400,
     *      ),
     * );
     * </code>
     * @remark <var>$_aDefaultKeys</var> defined by the system internally holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array();
    
    /**#@+
     * @remark The user will override this method in their class definition.
     */
    /**
     * The user constructor.
     * 
     * When the user defines a field type, they may use this instead of the real constructor 
     * so that they don't have to care about the internal parameters.
     * 
     * @since 3.1.3
     */
    protected function construct() {}    
        
    /**
     * Loads the field type necessary components.
     * 
     * This method is triggered when a field definition array that calls this field type is parsed. 
     * @since   3.0.0
     */     
    protected function setUp() {}
    
    /**
     * Returns the JavaScript output inside the `<script></script>` tags.
     * @since   3.0.0
     */
    protected function getScripts() { return ''; } 
    /**
     * Returns the CSS output specific to Internet Explorer inside the `<style></style>` tags.
     * @since   3.0.0
     */    
    protected function getIEStyles() { return ''; }
    /**
     * Returns the field type specific CSS output inside the `<style></style>` tags.
     * @since   3.0.0
     */    
    protected function getStyles() { return ''; }
    /**
     * Returns the field output.
     * @since   3.0.0
     */    
    protected function getField( $aField ) { return ''; }
    /**
     * Returns an array holding the urls of enqueuing scripts.
     * 
     * The returning array should consist of all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li>**src** - (required, string) The url or path of the target source file</li>
     *     <li>**handle_id** - (optional, string) The handle ID of the script.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**translation** - (optional, array) The translation array. The handle ID will be used for the object name.</li>
     *     <li>**in_footer** - (optional, boolean) Whether to enqueue the script before `</head>` or before `</body >` Default: `false`.</li>
     *     <li>**attributes** - (optional, array) [3.3.0+] attribute argument array. `array( 'async' => '', 'data-id' => '...' )`</li>
     * </ul>  
     * 
     * <h4>Examples</h4>
     * <code>
     * protected function getEnqueuingScripts() { 
     *      return array(
     *          array(     // if you need to set a dependency, pass as a custom argument array. 
     *              'src' => dirname( __FILE__ ) . '/asset/my_script.js',     // path or url
     *              'dependencies' => array( 'jquery' ) 
     *          ),
     *          dirname( __FILE__ ) . '/asset/my_another.js', // a string value of the target path or url will work as well.
     *      );
     * }
     * </code>
     */    
    protected function getEnqueuingScripts() { return array(); } // should return an array holding the urls of enqueuing items
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     * 
     * The returning array should consist of all numeric keys. Each element can be either a string( the url or the path of the source file) or an array of custom argument.
     * 
     * <h4>Custom Argument Array</h4>
     * <ul>
     *     <li>**src** - (required, string) The url or path of the target source file.</li>
     *     <li>**handle_id** - (optional, string) The handle ID of the stylesheet.</li>
     *     <li>**dependencies** - (optional, array) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
     *     <li>**version** - (optional, string) The stylesheet version number.</li>
     *     <li>**media** - (optional, string) the description of the field which is inserted into the after the input field tag.</li>
     *     <li>**attributes** - (optional, array) [3.3.0+] attributes array. `array( 'data-id' => '...' )`</li>
     * </ul>
     * 
     * <h4>Examples</h4>
     * <code>
     * protected function getEnqueuingStyles() { 
     *      return array(
     *          dirname( __FILE__ ) . '/asset/my_style.css',
     *          array(
     *              'src' => dirname( __FILE__ ) . '/asset/my_style2.css',
     *              'handle_id' => 'my_style2',
     *          ),
     *      );
     * }     
     * </code>
     */    
    protected function getEnqueuingStyles() { return array(); } // should return an array holding the urls of enqueuing items
    /**#@-*/
    
}