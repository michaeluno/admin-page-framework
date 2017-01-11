<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 */

/**
 * Provides an abstract base class to create custom fields in taxonomy definition pages.
 *
 * For products which can require WordPress 4.4 or above, it is recommended using [Term Meta](./package-AdminPageFramework_TermMeta_Documentation) instead.
 * 
 * <h2>Adding Form Fields</h2>
 * In the {@link AdminPageFramework_TaxonomyField_Controller::setUp()} method, use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add form fields
 * and the {@link AdminPageFramework_Factory_Controller::addSettingSections()} method for sections.
 * 
 * Instantiate the class with the following parameters.
 * 
 * 1. (string) taxonomy slug
 * 2. (string, optional) option key - by default the class name is applied.
 * 3. (string, optional) capability - default: manage_options
 * 4. (string, optional) text domain - default: admin-page-framework
 * 
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_TaxonomyField extends AdminPageFramework_TaxonomyField {
 *          
 *      public function setUp() {
 *
 *          $this->addSettingFields(
 *              array(
 *                  'field_id'      => 'text_field',
 *                  'type'          => 'text',
 *                  'title'         => 'Text Input',
 *              ),
 *              array(
 *                  'field_id'      => 'image_upload',
 *                  'type'          => 'image',
 *                  'title'         => __( 'Image Upload', 'admin-page-framework-loader' ),
 *                  'attributes'    => array(
 *                      'preview' => array(
 *                          'style' => 'max-width: 200px;',
 *                      ),
 *                  ),                
 *              )
 *          );     
 *      
 *      }
 *
 *  }
 *
 *  new APFDoc_TaxonomyField( 
 *      'apfdoc_taxonomy'   // taxonomy slug     
 *  );
 * </code>
 * 
 * <h2>Retrieving Field Values</h2>
 * <code>
 * $iTermID  = 8;   // you need to determine your term id 
 * $sFieldID = 'text_field';    // set your target field id / section id
 * $aOptions = get_option( 'APFDoc_TaxonomyField', array() );  // the key used here is just an example, you should set your own key here
 * $oUtil    = AdminPageFramework_Utility;
 * $vValue   = $oUtil->getElement( 
 *      $aOptions,  // subject array
 *      array( $iTermID, $sFieldID ),  // dimensional keys 
 *      'your default value' // default value
 * );
 * </code>
 * 
 * <h2>Hooks</h2>
 * <h3>Common Hooks</h3>
 * For common hooks throughout the other factory components, see [Factory](./package-AdminPageFramework.Common.Factory.html).
 * 
 * <h3>Factory Specific Hooks</h3>
 *
 * <h4>Filter Hooks</h4>
 * <ul>
 *     <li>**content_{instantiated class name}** – receives the entire output of the meta box. The first parameter: the output HTML string.</li>
 *     <li>**columns_{taxonomy slug}** – receives the header columns array. The first parameter: the header columns array.</li>
 *     <li>**columns_{instantiated class name}** – receives the header sortable columns array. The first parameter: the header columns array.</li>
 *     <li>**sortable_columns_{taxonomy slug}** – receives the header sortable columns array. The first parameter: the header columns array.</li>
 *     <li>**sortable_columns_{instantiated class name}** – receives the header columns array. The first parameter: the header columns array.</li>
 *     <li>**cell_{taxonomy slug}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 *     <li>**cell_{instantiated class name}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the column slug. The third parameter: the term ID.</li>
 *     <li>**cell_{instantiated class name}_{column slug}** – receives the cell output of the term listing table. The first parameter: the output string. The second parameter: the term ID.</li>
 *     <li>**footer_right_{instantiated class name}** – [3.5.5+] receives an HTML output for the right footer.</li> 
 *     <li>**footer_left_{instantiated class name}** – [3.5.5+] receives an HTML output for the left footer.</li> 
 * </ul> 
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/taxonomy_field.png
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/taxonomy_field_edit.png
 * @remark      Form sections are not supported.
 * @since       3.3.0
 * @package     AdminPageFramework
 * @subpackage  Factory/TaxonomyField
 * @heading     Taxonomy Field
 */
abstract class AdminPageFramework_TaxonomyField_Documentaion {}
