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
 * This factory is basically the same as the taxonomy field factory except the way to retrieve/save form data as this factory uses the term meta functions introduced in WordPress Core v4.4. 
 * Therefore, this requires WordPess 4.4 or above. For products which cannot require WordPress 4.4 or above, it is recommended using the [Taxonomy Field](./package-AdminPageFramework_TermMeta_Documentation) factory instead.
 * 
 * <h2>Adding Form Fields</h2>
 * In the {@link AdminPageFramework_TaxonomyField_Controller::setUp()} method, use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add form fields
 * and the {@link AdminPageFramework_Factory_Controller::addSettingSections()} method for sections.
 * 
 * Instantiate the class with the following parameters.
 * 
 * 1. (string) taxonomy slug
 * 2. (string, optional) capability - default: manage_options
 * 3. (string, optional) text domain - default: admin-page-framework
 * 
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_TermMeta extends AdminPageFramework_TermMeta {
 *          
 *      public function setUp() {
 *
 *          $this->addSettingFields(
 *              array(
 *                  'field_id'      => 'text_field',
 *                  'type'          => 'text',
 *                  'title'         => 'Text Input',
 *              ),
 *          );
 *      
 *          $this->addSettingSections(
 *              array(
 *                 'section_id'     => 'term_section',
 *                 'title'          => __( 'Section', 'admin-page-framework-loader' ),
 *              )      
 *          );
 *          
 *          $this->addSettingFields(
 *              'term_section', // section ID
 *              array(
 *                  'field_id'      => 'textarea_field',
 *                  'type'          => 'textarea',
 *                  'title'         => 'Text Area',
 *                  'attributes'    => array(
 *                      'cols' => 40,     
 *                  ),
 *              )
 *          );    
 *
 *      } 
 *      
 *  }
 *
 *  new APFDoc_TermMeta( 
 *      'apfdoc_taxonomy'   // taxonomy slug     
 *  );
 * </code>
 * 
 * <h2>Retrieving Field Values</h2>
 * Have a target term ID and the field/section ID. Then use the [get_term_meta()](https://developer.wordpress.org/reference/functions/get_term_meta/) function.
 * 
 * <code>
 * $value = get_term_meta( 
 *      $iTermID,       // term ID
 *      'text_field',  // field / section ID
 *      true    // single
 * );
 * </code>
 * 
 * <h2>Hooks</h2>
 * <h3>Common Hooks</h3>
 * For common hooks throughout the other factory components, see [Base Factory](./package-AdminPageFramework.Common.Factory.html).
 * 
 * <h3>Factory Specific Hooks</h3>
 * This factory uses the same hooks as the [Taxonomy Field](./package-AdminPageFramework_TaxonomyField_Documentation) factory. 
 * 
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/term_meta.png
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/term_meta_edit.png
 * @since       3.8.0
 * @package     AdminPageFramework
 * @subpackage  Factory/TermMeta
 * @heading     Term Meta
 */
abstract class AdminPageFramework_TermMeta_Documentaion {}
