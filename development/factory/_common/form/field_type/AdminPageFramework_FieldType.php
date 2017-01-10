<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
 * 
 */

/**
 * The base class for the users to create their custom field types.
 * 
 * When a framework user implements a custom field type into his/her work, this class may be extended to create a field definition class.
 * 
 * <h2>Creating a Custom Field Type</h2>
 * <ol>
 *      <li>Decide a unique field type slug that is going to be set in field definition arrays consisting of alphabets and underscores, such as `my_custom_field_type`. For example, the `autocomplete` custom field type uses the slug `autocomplete` and the date&time picker custom field type uses `date_time`.
 *  This sulug is the one the user sets in field definition arrays to the `type` argument.</li>
 *      <li>Define a PHP class that extends the `AdminPageFramework_FieldType` abstract class.
 *          <ol>
 *              <li>Extend the `AdminPageFramework_FieldType` class.
 *                  <code>
 *                  class MyCustomFieldType extends AdminPageFramework_FieldType {
 *                      // your code that defines the field type goes here
 *                  }</code>
 *              </li>
 *              <li>Set the field type slug decided in the above step such as `my_custom_field_type` to the `$aFieldTypeSlugs` property.
 *                  <code>
 *                      public $aFieldTypeSlugs = array( 'my_custom_field_type', );
 *                  </code>
 *              </li>
 *              <li>Define the default key-pairs of the field arguments. When you define a field, you create a definition array like this.
 *              <code>
 *                  array( 
 *                      'field_id'              => 'my_field_id', 
 *                      'type'                  => 'my_custom_field_type', 
 *                      'your_cusotm_key'       => ...,
 *                      'another_cusotm_key'    => ...,
 *                  )
 *              </code> 
 *              You can accept custom arguments by defining the key-value pairs in the `$aDefaultKeys` property array.
 *                  <h5>Example</h5>
 *                  <code>
 *                        protected $aDefaultKeys = array(
 *                          'label_min_width'   => 80,
 *                          'attributes'        => array(
 *                              'size'          => 10,
 *                              'maxlength'     => 400,
 *                          ),
 *                          'label'             => array(
 *                              'url'       => 'URL',
 *                              'title'     => 'Title',
 *                              // Add more as you need
 *                          ),
 *                      );
 *                  </code>
 *              </li>
 *              <li>(optional) Write additional code of procedural subroutines in the `setUp()` method. The `setUp()` method is performed when the field type definition is parsed by the framework.
 *                  <h5>Example</h5>
 *                  <code>
 *                  protected function setUp() {
 *                      wp_enqueue_script( 'jquery-ui-datepicker' );
 *                  }
 *                  </code>
 *              </li>
 *              <li>(optional) Add assets like scripts and styles with `getEnqueuingScripts()`, `getEnqueuingStyles()`.
 *                  <h5>Example</h5>
 *                  <code>
 *                   protected function getEnqueuingScripts() { 
 *                       return array(
 *                           array( 'src' => dirname( __FILE__ ) . '/js/datetimepicker-option-handler.js', ),
 *                       );
 *                   }    
 *                   protected function getEnqueuingStyles() { 
 *                       return array(
 *                           dirname( __FILE__ ) . '/css/jquery-ui-1.10.3.min.css',
 *                       );
 *                   }    
 *                  </code>
 *              </li>
 *              <li>(optional) Add inline scripts and styles with `getScripts()`, `getStyles()`.
 *                  <h5>Example</h5>
 *                  <code>
 *                  protected function getScripts() { 
 *                      return "
 *                          jQuery( document ).ready( function(){
 *                          console.log( 'debugging: loaded' );
 *                          });        
 *                      " . PHP_EOL;
 *                  }
 *                  protected function getStyles() {
 *                      return ".admin-page-framework-input-label-container.my_custom_field_type { padding-right: 2em;' }";
 *                  }
 *                  </code>
 *              </li>
 *              <li>Construct the output HTML structure with the passed `$aField` field definition array in the `getField()` method.
 *                  <h5>Example</h5>
 *                  <code>
 *                      protected function getField( $aField ) { 
 *                          return 
 *                              $aField['before_label']
 *                              . $aField['before_input']
 *                              . "<div class='repeatable-field-buttons'></div>"    // the repeatable field buttons
 *                              . $this->_getInputs( $aField )
 *                              . $aField['after_input']
 *                              . $aField['after_label'];      
 *                      }    
 *                          private function _getInputs( $aField ) {
 *                              $_aOutput = array();
 *                              foreach( ( array ) $aField['label'] as $_sSlug => $_sLabel ) {
 *                                  $_aAttributes = isset( $aField['attributes'][ $_sSlug ] ) && is_array( $aField['attributes'][ $_sSlug ] )
 *                                      ? $aField['attributes'][ $_sSlug ] + $aField['attributes']
 *                                      : $aField['attributes'];
 *                                  $_aAttributes = array(
 *                                      'tyle'  => 'text',
 *                                      'name'  => "{$_aAttributes['name']}[{$_sSlug}]",
 *                                      'id'    => "{$aField['input_id']}_{$_sSlug}",
 *                                      'value' => isset( $aField['attributes']['value'][ $_sSlug ] ) ? $aField['attributes']['value'][ $_sSlug ] : '',
 *                                  ) + $_aAttributes;
 *                                  $_aOutput[] = 
 *                                      "<div class='admin-page-framework-input-label-container my_custom_field_type'>"
 *                                          . "<label for='{$aField['input_id']}_{$_sSlug}'>"
 *                                              . "<span class='admin-page-framework-input-label-string' style='min-width:" . $aField['label_min_width'] . "px;'>" 
 *                                                  . $_sLabel
 *                                              . "</span>" . PHP_EOL                    
 *                                              . "<input " . $this->getAttributes( $_aAttributes ) . " />"
 *                                          . "</label>"
 *                                      . "</div>";                
 *                              }
 *                              return implode( PHP_EOL, $_aOutput );
 *                          }
 *                  </code>
 *              </li>
 *          </ol>
 *      </li>
 *      <li>Instantiate the field type class by passing the instantiated class name of the framework class. See the below section to see how to include a custom field type.</li>
 * </ol>
 * 
 * <h3>Including a Custom Field Type</h3>
 * <ol>
 *     <li>
 *         Include the definition file and instantiate the class in the script (plugin,theme etc.).
 *         <code>
 *          // pass the PHP class name that extends the framework's class to the first parameter.
 *         new MyCustomFieldType( 'MY_FRAMEWORK_CLASS_NAME' );   
 *         </code>
 *     </li>
 *     <li>
 *         Define fields with the custom field type with the `addSettingFields()` method in the framework extending class.
 *         <code>
 *         $this->addSettingFields(
 *              array(  
 *                  'field_id'     =>  'my_field_id',
 *                  'section_id'   =>  'my_section_id',
 *                  'type'         =>  'my_custom_field_type',    // <-- here put the field type slug
 *                  '...'          => '...'
 *              )
 *         );
 *         </code>
 *     </li>
 * </ol>
 * 
 * @abstract
 * @package     AdminPageFramework
 * @subpackage  Common/Form/FieldType
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
   
    /**
     * Responds to a call back which is triggered when a field is registered.
     * @since       3.5.0
     * @since       3.8.14      Removed the type-hint.
     * @callback    fieldtype   hfDoOnRegistration
     */
    public function _replyToDoOnFieldRegistration( $aField ) {
        return $this->doOnFieldRegistration( $aField ); 
    }
   
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
     *     <li>**attributes** - deprecated 3.7.0+ (optional, array) [3.3.0+] attribute argument array. `array( 'async' => '', 'data-id' => '...' )`</li>
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
     *     <li>**attributes** - deprecated 3.7.0+ (optional, array) [3.3.0+] attributes array. `array( 'data-id' => '...' )`</li>
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
    
    /**
     * Called when the given field of this field type is registered.
     * @since       3.5.0
     * @since       3.5.1       Removed a type hint in the first parameter.
     */
    protected function doOnFieldRegistration( $aField ) {}
    /**#@-*/
    
}
