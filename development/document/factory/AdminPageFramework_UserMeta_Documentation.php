<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides an abstract base class to add form fields in the user profile page of the administration area.
 *
 * Define a class by extending the {@link AdminPageFramework_UserMeta} class and do set-ups in the {@link AdminPageFramework_UserMeta_Controller::setUp()} method.
 *
 * <h2>Adding Fields to a User Profile Editing Page</h2>
 * 1. Define a new class by extending the {@link AdminPageFramework_UserMeta} class.
 * 2. Override the {@link AdminPageFramework_UserMeta_Controller::setUp()} method and in the method definition, use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add form fields.
 *
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_ExampleUserMeta extends AdminPageFramework_UserMeta {
 *
 *      public function setUp() {
 *
 *          $this->addSettingFields(
 *              array(
 *                  'field_id'      => 'text_area',
 *                  'type'          => 'textarea',
 *                  'title'         => 'Text Area',
 *              )
 *          );
 *
 *          $this->addSettingSections(
 *              array(
 *                  'section_id'    => 'my_section',
 *                  'title'         => 'My Section',
 *              )
 *          );
 *          $this->addSettingFields(
 *              'my_section',
 *              array(
 *                  'field_id'      => 'text_field',
 *                  'type'          => 'text',
 *                  'title'         => 'Text',
 *              )
 *          );
 *
 *      }
 *
 *  }
 *  new APFDoc_ExampleUserMeta;
 * </code>
 *
 * <h2>Retrieving Meta Values</h2>
 * The fields values of user meta data are stored in the [usermeta](https://codex.wordpress.org/Database_Description#Table:_wp_usermeta) table associated with the user ID.
 * So use the [get_user_meta()](https://codex.wordpress.org/Function_Reference/get_user_meta) function to retrieve the field values.
 * Please note that if you set a section, the value comes as a multi-dimensional array.
 *
 * <h3>Example</h3>
 * <code>
 * $_sTextArea = get_user_meta(
 *      1,      // user ID
 *      'text_area' // field id (user meta key)
 *      true    // single
 * );
 * $_aMySection = get_user_meta(
 *      1,      // user ID
 *      'my_section' // section id (user meta key)
 *      true    // single
 * );
 * </code>
 *
 * <h2>Hooks</h2>
 * See the Hooks section of the [Factory package summary](./package-AdminPageFramework.Common.Factory.html) page.
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/factory/user_meta.png
 * @since           3.3.0
 * @package         AdminPageFramework/Factory/UserMeta
 * @heading         User Meta
 */
abstract class AdminPageFramework_UserMeta_Documentation {}
