<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base to be extend to create field types.
 * 
 * <h3>Steps to Write a Custom Field Type</h3>
 * <ol>
 *      <li>Decide a unique field type slug that is going to be set in field definition arrays consisting of alphabets and underscores, such as `my_custom_field_type`. For example, the `autocomplete` custom field ype uses the slug `autocomplete` and the date&time picker custom field type uses `date_time`.</li>
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
 *              <li>Define the default key-pairs of the field arguments. When you define a field in Admin Page Framework, you create a definition array like 
 *              <code>
 *                  array( 
 *                      'field_id'              => 'my_field_id', 
 *                      'type'                  => 'my_custom_field_type', 
 *                      'your_cusotm_key'       => ...,
 *                      'another_cusotm_key'    => ...,
 *                  )
 *               </code> 
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
 *              <li>(optional) Write additional code of procedural subroutine in the `setUp()` method that will be performed when the field type definition is parsed by the framework.
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
 *                                              . "<input " . $this->generateAttributes( $_aAttributes ) . " />"
 *                                          . "</label>"
 *                                      . "</div>";                
 *                              }
 *                              return implode( PHP_EOL, $_aOutput );
 *                          }
 *                  </code>
 *              </li>
 *          </ol>
 *      </li>
 *      <li>Instantiate the field type by passing the instantiated class name of the framework class. See the below section of how to include a custom field type.</li>
 * </ol>
 * 
 * <h3>Steps to Include a Custom Field Type</h3>
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
 * @package     AdminPageFramework
 * @subpackage  FieldType
 * @since       3.3.0      
 * @heading     Field Type
 */
abstract class AdminPageFramework_FieldType_Documentation {}