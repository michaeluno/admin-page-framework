<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base class to create meta boxes in post editing pages.
 *
 * Define a new class by extending the {@link AdminPageFramework_MetaBox} class and do set-ups in the {@link AdminPageFramework_MetaBox_Controller::setUp()} method.
 *
 * <h2>Creating Forms in a Post Meta Box</h2>
 * <p>In the {@link AdminPageFramework_MetaBox_Controller::setUp()} method, use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add form fields
 * and the {@link AdminPageFramework_Factory_Controller::addSettingSections()} method for sections.</p>
 * 
 * <p>For parameter details, see the links of the methods.</p>
 * 
 * <p>Sections are optional. It is recommended adding a underscore prefix to IDs of sections and fields which do not have a section. Otherwise, it is listed as a custom field in the Custom Fields built-in meta box.</p>
 * 
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_AddPostMetaBox extends AdminPageFramework_MetaBox {
 *      
 *      public function setUp() {
 *          
 *          $this->addSettingSections(
 *              array(
 *                  'section_id'        => '_my_post_meta_section',
 *                  'title'             => 'My Post Meta Section',
 *              )
 *          );
 *          $this->addSettingFields(
 *              'my_post_meta_section', // section id
 *              array(
 *                  'field_id'          => 'textarea',
 *                  'title'             => __( 'Text Area', 'admin-page-framework-loader' ),
 *                  'type'              => 'textarea',
 *                  'rich'              => true,
 *              ),
 *              array(
 *                  'field_id'          => 'color',
 *                  'title'             => __( 'Color', 'admin-page-framework-loader' ),
 *                  'type'              => 'color',
 *              )
 *          );
 *    
 *      }    
 *      
 *  }
 *  new APFDoc_AddPostMetaBox(
 *      null,           // (null|string) meta box ID  - pass null to auto-generate
 *      'My Meta Box',  // (string) title
 *      'post',         // (array|string) post type slug(s)
 *      'normal',       // (string) context           - either 'normal', 'side', or 'advanced'.
 *      'high'          // (string) priority          - either 'high', 'low', or 'default'.
 *  );
 * </code>
 * 
 * <h2>Retrieving Field Values</h2>
 * <p>They are stored in the [post_meta](https://codex.wordpress.org/Database_Description#Table:_wp_postmeta) table associated with the post ID.
 * So use [get_post_meta()](https://developer.wordpress.org/reference/functions/get_post_meta/) function by specifying the section or field ID.
 * </p>
 * 
 * <h3>Example</h3>
 * <code>
 * $_sFieldValue = get_post_meta( 
 *      1234,   // post ID
 *      '_my_field_id', // field ID (meta key)
 *      true        // single
 * );
 * $_aSectionValues = get_post_meta( 
 *      1234,   // post ID
 *      '_my_section_id', // field ID (meta key)
 *      true        // single
 * );
 * </code>
 * 
 * <h2>Hooks</h2>
 * See the Hooks section of the [Factory package summary](./package-AdminPageFramework.Common.Factory.html) page.
 * 
 * @image           http://admin-page-framework.michaeluno.jp/image/factory/post_meta_box.png
 * @since           3.3.0
 * @package         AdminPageFramework
 * @subpackage      Factory/MetaBox
 * @heading         Post Meta Box
 */
abstract class AdminPageFramework_MetaBox_Documentation {}
