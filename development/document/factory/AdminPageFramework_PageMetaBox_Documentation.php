<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides an abstract base class to create meta boxes in generic admin pages created by the framework.
 *
 * <h2>Adding a Meta Box to an Admin Page</h2>
 *
 * 1. Know a page slug of the target page.
 * 2. Define a class by extending the {@link AdminPageFramework_PageMetaBox} class.
 * 3. Instantiate the class by passing necessary parameters including the target page slug. For parameter details, see {@link AdminPageFramework_PageMetaBox::__construct()}.
 *
 * <h2>Adding Form Fields to a Page Meta Box</h2>
 *
 * In the setUp method, use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add fields and {@link AdminPageFramework_Factory_Controller::addSettingSections()} method to add sections.
 *
 * <h2>Retrieving Form Data of Page Meta Boxes</h2>
 *
 * The form data are stored in the [options](https://codex.wordpress.org/Database_Description#Table:_wp_options) table in a single row with the key of the target admin page class.
 * If the target admin page class does not set a key, it is the class name by default. So use the [get_option()](https://codex.wordpress.org/Function_Reference/get_option) function to retrieve the form data.
 * To extract elements from the returned multi-dimensional array, you may want to use the `AdminPageFramework_Utility::getElement()` method.
 *
 * <h2>Example</h2>
 * <h3>Create an Admin Page and add Page Meta Boxes</h3>
 * <code>
 *  class APFDoc_AdminPageForPageMetaBoxes extends AdminPageFramework {
 *
 *      public function setUp() {
 *
 *          // Create a top-level menu.
 *          $this->setRootMenuPage( 'Page Meta Box' );
 *
 *          // Add sub menu items.
 *          $this->addSubMenuItems(
 *              array(
 *                  'title'         => 'Page for Meta Box',    // page title
 *                  'page_slug'     => 'page_for_meta_box',    // page slug
 *              )
 *          );
 *
 *      }
 *
 *  }
 *  new APFDoc_AdminPageForPageMetaBoxes;
 *
 *  class APFDoc_PageMetaBox extends AdminPageFramework_PageMetaBox {
 *
 *      public function setUp() {
 *
 *          $this->addSettingFields(
 *              array(
 *                  'field_id'          => 'radio_field',
 *                  'type'              => 'radio',
 *                  'title'             => 'Radio',
 *                  'label'             => array(
 *                      'a' => 'Apple',
 *                      'b' => 'Banana',
 *                      'c' => 'Cherry',
 *                  ),
 *                  'default'           => 'b',
 *              ),
 *              array(
 *                  'field_id'          => 'checkbox_field',
 *                  'type'              => 'checkbox',
 *                  'title'             => 'Check Box',
 *                  'label'             => 'Check me.'
 *              )
 *          );
 *
 *      }
 *
 *  }
 *  new APFDoc_PageMetaBox(
 *      null,                       // (null|string) meta box id - passing null will make it auto generate
 *      'Page Meta Box Example',    // (sting) title
 *      'page_for_meta_box',        // (array|string) target page slug
 *      'normal',                   // (string) context - either 'normal', 'side', or 'advanced'.
 *      'default'                   // (string) priority - either 'high', 'low', or 'default'.
 *  );
 *
 *  class APFDoc_SidePageMetaBox extends AdminPageFramework_PageMetaBox {
 *
 *      public function setUp() {
 *
 *          $this->addSettingSections(
 *              array(
 *                  'section_id'        => 'example_section',
 *                  'title'             => 'Example Section',
 *              )
 *          );
 *
 *          $this->addSettingFields(
 *              'example_section',  // target section
 *              array(
 *                  'field_id'          => 'example_image',
 *                  'type'              => 'image',
 *                  'title'             => __( 'image', 'admin-page-framework-tutorial' ),
 *              ),
 *              array(
 *                  'field_id'          => '__submit',
 *                  'type'              => 'submit',
 *                  'save'              => false,
 *                  'show_title_column' => false,
 *                  'label_min_width'   => '',
 *                  'attributes'        => array(
 *                      'field' => array(
 *                          'style' => 'float:right; width:auto;',
 *                      ),
 *                  ),
 *              )
 *          );
 *
 *      }
 *
 *  }
 *  new APFDoc_SidePageMetaBox(
 *      null,                       // (null|string) meta box id - passing null will make it auto generate
 *      'Side Page Meta Box',       // (sting) title
 *      'page_for_meta_box',        // (array|string) target page slug
 *      'side',                     // (string) context - either 'normal', 'side', or 'advanced'.
 *      'high'                      // (string) priority - either 'high', 'low', or 'default'.
 *  );
 * </code>
 * <h3>Retrieve Saved Options</h3>
 * <code>
 * $_aData      = get_option( 'APFDoc_AdminPageForPageMetaBoxes', array() );
 * $_oUtil      = new AdminPageFramework_Utility;
 * $_sRadio     = $_oUtil->getElement( $_aData, 'radio_field' );
 * $_sImageURL  = $_oUtil->getElement( $_aData, array( 'example_section', 'example_image' ) );
 * </code>
 *
 * <h2>Hooks</h2>
 * See the Hooks section of the [Factory package summary](./package-AdminPageFramework.Common.Factory.html) page.
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/page_meta_box.png
 * @since       3.3.0
 * @package     AdminPageFramework/Factory/PageMetaBox
 * @heading     Page Meta Box
 */
abstract class AdminPageFramework_PageMetaBox_Documentation {}
