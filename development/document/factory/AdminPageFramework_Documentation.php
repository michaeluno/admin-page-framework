<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 */

/**
 * Handles creation of admin pages and setting forms.
 *
 * This class is an abstract class that provides shared methods and should be extended.
 * Override the {@link AdminPageFramework_Controller::setUp()} method to add pages.
 *
 * <h2>Creating Admin Pages</h2>
 *
 * 1. Define your own class by extending the {@link AdminPageFramework} class.
 * 2. Set a top-level menu with the {@link AdminPageFramework_Controller_Menu::setRootMenuPage()} method.
 * 3. Add page items with the {@link AdminPageFramework_Controller_Menu::addSubMenuItems()} method.
 * 4. To insert contents, use the {@link AdminPageFramework::content()} method and return custom outputs.
 *
 * For details of method parameters, see the links of the methods.
 *
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_Create extends AdminPageFramework {
 *
 *      public function setUp() {
 *
 *          // Create a top-level menu.
 *          $this->setRootMenuPage( 'My Admin Pages' );
 *
 *          // Add sub menu items.
 *          $this->addSubMenuItems(
 *              array(
 *                  'title'         => 'My Page A',    // page title
 *                  'page_slug'     => 'my_page_a',    // page slug
 *              ),
 *              array(
 *                  'title'         => 'My Page B',    // page title
 *                  'page_slug'     => 'my_page_b',    // page slug
 *              )
 *          );
 *
 *      }
 *
 *      public function content( $sHTML ) {
 *          return $sHTML . "<p>Hello world!</p>";
 *      }
 *
 *  }
 *  new APFDoc_Create;
 * </code>
 *
 * To add pages to existent built-in menus, pass the value from the followings.
 *
 * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
 *
 * e.g. `$this->setRootMenuPage( 'Appearance' );`
 *
 * <h2>Adding In-page Tabs in Admin Pages</h2>
 *
 * To add in-page tabs to created admin pages. use the {@link AdminPageFramework_Controller_Page::addInPageTabs()} method.
 * Set the target page slug to the first parameter and tab definition arrays to the rest. For details of arguments, see the link of the method.
 *
 * To insert custom contents, define a method with a name made up of `content_` + `page slug` + `tab slug`. For example, if your page slug is `my_page` and the tab slug is `my_tab`,
 * then the method name would be `content_my_page_my_tab()`. Then return custom outputs from the method.
 *
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_AddInPageTabs extends AdminPageFramework {
 *
 *      public function setUp() {
 *
 *          // Create a top-level menu.
 *          $this->setRootMenuPage( 'My Admin Pages' );
 *
 *          // Add sub menu items.
 *          $this->addSubMenuItems(
 *              array(
 *                  'title'         => 'My Page',    // page title
 *                  'page_slug'     => 'my_page',    // page slug
 *              )
 *          );
 *
 *          // Add in-page tabs
 *          $this->addInPageTabs(
 *              'my_page',    // target page slug
 *              array(
 *                  'title'         => 'My Tab A',    // tab title
 *                  'tab_slug'      => 'my_tab_a',    // tab slug
 *              ),
 *              array(
 *                  'title'         => 'My Tab B',    // tab title
 *                  'tab_slug'      => 'my_tab_b',    // tab slug
 *              )
 *          );
 *
 *      }
 *
 *      public function content_my_page_my_tab_a( $sHTML ) {
 *          return $sHTML . "<p>This message is shown in My Tab A.</p>";
 *      }
 *
 *  }
 *  new APFDoc_AddInPageTabs;
 * </code>
 *
 * <h2>Creating Forms in Admin Pages</h2>
 * <p>To create a form, you need to add form fields as required and form sections as optional.
 * Use the {@link AdminPageFramework_Factory_Controller::addSettingField()} method to add form fields.</p>
 *
 * <p>It is recommended you build forms in the `load()` method which gets triggered when the page starts loading before the HTTP header is sent.
 * The `setUp()` method is called throughout the admin area to build menus which is displayed in almost all the admin area.</p>
 *
 * For details of field arguments, see the {@link AdminPageFramework_Factory_Controller::addSettingField()} method.
 *
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_CreateForm extends AdminPageFramework {
 *
 *      public function setUp() {
 *
 *          // Create a top-level menu.
 *          $this->setRootMenuPage( 'My Form' );
 *
 *          // Add sub menu items.
 *          $this->addSubMenuItems(
 *              array(
 *                  'title'         => 'My Form',    // page title
 *                  'page_slug'     => 'my_form',    // page slug
 *              )
 *          );
 *
 *      }
 *
 *      public function load() {
 *
 *          $this->addSettingSections(
 *              array(
 *                  'section_id'        => 'my_section',
 *                  'title'             => 'My Section',
 *              )
 *          );
 *
 *          $this->addSettingFields(
 *              'my_section',   // target section ID
 *              array(
 *                  'field_id'  => 'my_field_a',
 *                  'title'     => 'Field A',
 *                  'type'      => 'text',
 *              ),
 *              array(
 *                  'field_id'  => 'my_field_b',
 *                  'title'     => 'Field B',
 *                  'type'      => 'text',
 *              ),
 *              array(
 *                  'field_id'  => '_submit',
 *                  'type'      => 'submit',
 *                  'save'      => false,
 *              )
 *          );
 *
 *      }
 *
 *  }
 *  new APFDoc_CreateForm;
 * </code>
 *
 * <h2>Retrieving Form Data</h2>
 *
 * By default, the form data are saved in the [options](https://codex.wordpress.org/Database_Description#Table:_wp_options) table in a single row with the key of the extended class name.
 * A custom option key can be set via the first parameter of the constructor. e.g. `APFDoc_CreateForm( 'my_option_key' )`
 *
 * So use the [get_option()](https://codex.wordpress.org/Function_Reference/get_option) function to retrieve the data.
 * The data comes as a multi-dimensional array. To extract values from it, you may want to use the `AdminPageFramework_Utility::getElement()` method.
 *
 * <h3>Example</h3>
 * <code>
 * $_aData         = get_option( 'APFDoc_CreateForm', array() );
 * $_oUtil         = new AdminPageFramework_Utility;
 * $_sMyFieldValue = $_oUtil->getElement(
 *      $_aData,    // subject array
 *      array( 'my_section_id', 'my_field_id' ),    // dimensional path
 *      'my default value'         // default value
 * );
 * </code>
 * <code>
 * new APFDoc_CreateForm( 'my_custom_option_key' );
 * $_aData  = get_option( 'my_custom_option_key' );
 * </code>
 *
 * <h2>Hooks</h2>
 * <h3>Common Hooks</h3>
 * For common hooks throughout the other factory components, see [Base Factory](./package-AdminPageFramework.Common.Factory.html).
 *
 * <h3>Factory Specific Hooks</h3>

 * <h4> Action Hooks</h4>
 * <ul>
 *     <li>**load_{page slug}** – [2.1.0+] triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework. The first parameter: class object [3.1.2+].</li>
 *     <li>**load_{page slug}_{tab slug}** – [2.1.0+] triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework. The first parameter: class object [3.1.2+].</li>
 *     <li>**do_before_{instantiated class name}** – triggered before rendering the page. It applies to all the pages created by the instantiated class object. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_before_{page slug}** – triggered before rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_before_{page slug}_{tab slug}** – triggered before rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_form_{instantiated class name}** – triggered right after the form opening tag. It applies to all the pages created by the instantiated class object. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_form_{page slug}** – triggered right after the form opening tag. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_form_{page slug}_{tab slug}** – triggered right after the form opening tag. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_{instantiated class name}** – triggered in the middle of rendering the page. It applies to all the pages created by the instantiated class object. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_{page slug}** – triggered in the middle of rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_{page slug}_{tab slug}** – triggered in the middle of rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_after_{instantiated class name}** – triggered after rendering the page. It applies to all the pages created by the instantiated class object. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_after_{page slug}** – triggered after rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**do_after_{page slug}_{tab slug}** – triggered after rendering the page. The class object will be passed to the first parameter [3.1.3+].</li>
 *     <li>**submit_{instantiated class name}_{submit input id}** – [3.0.0+] **Deprecated**[3.3.1+] triggered after the form is submitted with the submit button of the specified input id.</li>
 *     <li>**submit_{instantiated class name}_{submit field id}** – [3.0.0+] triggered after the form is submitted and before the options are saved when the submit button of the specified field without a section</li>
 *     <li>**submit_{instantiated class name}_{submit section id}_{submit field id}** – [3.0.0+] triggered after the form is submitted and before the options are saved with the submit button of the specified section and field.</li>
 *     <li>**submit_{instantiated class name}_{submit section id}** – [3.0.0+] triggered after the form is submitted and before the options are saved with the submit button of the specified section.</li>
 *     <li>**submit_{instantiated class name}** – [3.0.0+] triggered after the form is submitted and before the options are saved.</li>
 *     <li>**submit_after_{instantiated class name}_{submit field id}** – [3.3.1+] triggered after the form is submitted and the options are saved in the database with the submit button of the specified field without a section.</li>
 *     <li>**submit_after_{instantiated class name}_{submit section id}_{submit field id}** – [3.3.1+] triggered after the form is submitted and the options are saved with the submit button of the specified section and field.</li>
 *     <li>**submit_after_{instantiated class name}_{submit section id}** – [3.3.1+] triggered after the form is submitted and the options are saved with the submit button of the specified section.</li>
 *     <li>**submit_after_{instantiated class name}** – [3.3.1+] triggered after the form is submitted and after the options are saved.</li>
 *     <li>**reset_{instantiated class name}_{submit field id}** – [3.5.9+] triggered when resetting option with the `reset` argument of the `submit` field type of the specified field id which does not have a section.</li>
 *     <li>**reset_{instantiated class name}_{submit section id}_{submit field id}** – [3.5.9+] triggered when resetting option with the `reset` argument of the `submit` field type of the submit button of the specified section and field.</li>
 *     <li>**reset_{instantiated class name}_{submit section id}** – [3.5.9+] triggered when resetting option with the `reset` argument of the `submit` field type of the specified section.</li>
 *     <li>**reset_{instantiated class name}** – [3.5.9+] triggered when resetting option with the `reset` argument of the `submit` field type..</li>
 * </ul>
 *
 * <h4>Filter Hooks</h4>
 * <ul>
 *     <li>**content_top_{page slug}_{tab slug}** – receives the output of the top part of the page. [3.0.0+] Changed the name from head_{...}.</li>
 *     <li>**content_top_{page slug}** – receives the output of the top part of the page. [3.0.0+] Changed the name from head_{...}.</li>
 *     <li>**content_top_{instantiated class name}** – receives the output of the top part of the page, applied to all pages created by the instantiated class object. [3.0.0+] Changed the name from head_{...}.</li>
 *     <li>**content_{page slug}_{tab slug}** – receives the output of the middle part of the page including form input fields.</li>
 *     <li>**content_{page slug}** – receives the output of the middle part of the page including form input fields.</li>
 *     <li>**content_{instantiated class name}** – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.</li>
 *     <li>**content_bottom_{page slug}_{tab slug}** – receives the output of the bottom part of the page. [3.0.0+] Changed the name from foot_{...}.</li>
 *     <li>**content_bottom_{page slug}** – receives the output of the bottom part of the page. [3.0.0+] Changed the name from foot_{...}.</li>
 *     <li>**content_bottom_{instantiated class name}** – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object. [3.0.0+] Changed the name from foot_{...}.</li>
 *     <li>**pages_{instantiated class name}** – receives the registered page arrays. The first parameter: pages container array.</li>
 *     <li>**tabs_{instantiated class name}_{page slug}** – receives the registered in-page tab arrays. The first parameter: tabs container array.</li>
 *     <li>**options_update_status_{instantiated class name}** – [3.4.1+] receives an array of options update status. First parameter: (array) an array of options update status.</li>
 *     <li>**options_update_status_{page slug}** – [3.4.1+] receives an array of options update status. First parameter: (array) an array of options update status.</li>
 *     <li>**options_update_status_{page slug}_{tab slug}** – [3.4.1+] receives an array of options update status. First parameter: (array) an array of options update status.</li>
 *     <li>**setting_update_url_{instantiated class name}** – [3.4.5+] receives the url that is used after the form is submitted.</li>
 *     <li>**validation_{page slug}_{tab slug}** – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_{page slug}** – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_{instantiated class name}** – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The third parameter: ( object ) [3.1.0+] the caller object.</li>
 *     <li>**validation_saved_options_{page slug}** – [3.0.0+] receives the saved form options as an array of the page. The first parameter: the stored options array of the page. The second parameter: the caller object.</li>
 *     <li>**validation_saved_options_{page slug}_{tab slug}** – [3.0.0+] receives the saved form options as an array of the tab. The first parameter: the stored options array of the tab. The second parameter: the caller object.</li>
 *     <li>**validation_saved_options_without_dynamic_elements_{page slug}** – [3.4.1+] receives the saved form options as an array of the page without dynamic elements such as repeatable and sortable fields. The first parameter: the stored options array of the page. The second parameter: the caller object.</li>
 *     <li>**validation_saved_options_without_dynamic_elements_{page slug}_{tab slug}** – [3.4.1+] receives the saved form options as an array of the tab without dynamic elements such as repeatable and sortable fields. The first parameter: the stored options array of the tab. The second parameter: the caller object.</li>
 *     <li>**style_{page slug}_{tab slug}** – receives the output of the CSS rules applied to the tab page of the slug.</li>
 *     <li>**style_{page slug}** – receives the output of the CSS rules applied to the page of the slug.</li>
 *     <li>**script_{page slug}_{tab slug}** – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 *     <li>**script_{page slug}** – receives the output of the JavaScript script applied to the page of the slug.</li>
 *     <li>**export_{instantiated class name}_{input id}** – [2.1.5+] **Deprecated**[3.3.1+]  receives the exporting array submitted from the specific export button.</li>
 *     <li>**export_{instantiated class name}_{field id}** – [2.1.5+] receives the exporting array submitted from the specific field that does not have a section.</li>
 *     <li>**export_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the exporting array submitted from the specific field that has a section.</li>
 *     <li>**export_{page slug}_{tab slug}** – receives the exporting array sent from the tab page.</li>
 *     <li>**export_{page slug}** – receives the exporting array submitted from the page.</li>
 *     <li>**export_{instantiated class name}** – receives the exporting array submitted from the plugin.</li>
 *     <li>**export_name_{instantiated class name}_{input id}** – **Deprecated**[3.3.1+] receives the exporting file name submitted the specified input id.</li>
 *     <li>**export_name_{instantiated class name}_{field id}** – receives the exporting file name submitted from the specific field that does not have a section.</li>
 *     <li>**export_name_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the exporting file name submitted from the specific field that has a section.</li>
 *     <li>**export_name_{page slug}_{tab slug}** – receives the exporting file name submitted from the tab page.</li>
 *     <li>**export_name_{page slug}** – receives the exporting file name submitted from the page.</li>
 *     <li>**export_name_{instantiated class name}** – receives the exporting file name submitted from the script.</li>
 *     <li>**export_format_{instantiated class name}_{input id}** – **Deprecated**[3.3.1+] receives the exporting file format submitted from the specific export button.</li>
 *     <li>**export_format_{instantiated class name}_{field id}** – receives the exporting file format submitted from the specific field that does not have a section.</li>
 *     <li>**export_format_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the exporting file format submitted from the specific field that has a section.</li>
 *     <li>**export_format_{page slug}_{tab slug}** – receives the exporting file format sent from the tab page.</li>
 *     <li>**export_format_{page slug}** – receives the exporting file format submitted from the page.</li>
 *     <li>**export_format_{instantiated class name}** – receives the exporting file format submitted from the plugin.</li>
 *     <li>**export_header_{instantiated class name}_{field id}** – [3.5.4+] receives an array defining the HTTP header.</li>
 *     <li>**export_header_{instantiated class name}_{section id}_{field id}** – [3.5.4+] receives an array defining the HTTP header.</li>
 *     <li>**export_header_{page slug}_{tab slug}** – [3.5.4+] receives an array defining the HTTP header.</li>
 *     <li>**export_header_{page slug}** – [3.5.4+] receives an array defining the HTTP header.</li>
 *     <li>**export_header_{instantiated class name}** – [3.5.4+] receives an array defining the HTTP header.</li>
 *     <li>**import_{instantiated class name}_{input id}** – [2.1.5+] **Deprecated**[3.3.1+] receives the importing array submitted from the specific import button.</li>
 *     <li>**import_{instantiated class name}_{field id}** – [2.1.5+] receives the importing array submitted from the specific import field that does not have a section.</li>
 *     <li>**import_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the importing array submitted from the specific import field that has a section.</li>
 *     <li>**import_{page slug}_{tab slug}** – receives the importing array submitted from the tab page.</li>
 *     <li>**import_{page slug}** – receives the importing array submitted from the page.</li>
 *     <li>**import_{instantiated class name}** – receives the importing array submitted from the plugin.</li>
 *     <li>**import_mime_types_{instantiated class name}_{input id}** – [2.1.5+] **Deprecated**[3.3.1+] receives the mime types of the import data submitted from the specific import button.</li>
 *     <li>**import_mime_types_{instantiated class name}_{field id}** – [2.1.5+] receives the mime types of the import data submitted from the specific import field that does not have a section.</li>
 *     <li>**import_mime_types_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the mime types of the import data submitted from the specific import field that has a section.</li>
 *     <li>**import_mime_types_{page slug}_{tab slug}** – receives the mime types of the import data submitted from the tab page.</li>
 *     <li>**import_mime_types_{page slug}** – receives the mime types of the import data submitted from the page.</li>
 *     <li>**import_mime_types_{instantiated class name}** – receives the mime types of the import data submitted from the plugin.</li>
 *     <li>**import_format_{instantiated class name}_{input id}** – [2.1.5+] **Deprecated**[3.3.1+] receives the import data format submitted from the specific import button.</li>
 *     <li>**import_format_{instantiated class name}_{field id}** – [2.1.5+] receives the import data format submitted from the specific import field that does not have a section.</li>
 *     <li>**import_format_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the import data format submitted from the specific import field that has a section.</li>
 *     <li>**import_format_{page slug}_{tab slug}** – receives the import data format submitted from the tab page.</li>
 *     <li>**import_format_{page slug}** – receives the import data format submitted from the page.</li>
 *     <li>**import_format_{instantiated class name}** – receives the import data format submitted from the plugin.</li>
 *     <li>**import_option_key_{instantiated class name}_{input id}** – [2.1.5+] **Deprecated**[3.3.1+] receives the option array key of the importing array submitted from the specific import button.</li>
 *     <li>**import_option_key_{instantiated class name}_{field id}** – [2.1.5+] receives the option array key of the importing array submitted from the specific import field that does not have a section.</li>
 *     <li>**import_option_key_{instantiated class name}_{section id}_{field id}** – [3.0.0+] receives the option array key of the importing array submitted from the specific import field that has a section.</li>
 *     <li>**import_option_key_{page slug}_{tab slug}** – receives the option array key of the importing array submitted from the tab page.</li>
 *     <li>**import_option_key_{page slug}** – receives the option array key of the importing array submitted from the page.</li>
 *     <li>**import_option_key_{instantiated class name}** – receives the option array key of the importing array submitted from the plugin.</li>
 *     <li>**footer_left_{instantiated class name}** – [3.5.5+] receives an HTML output for the left footer.</li>
 *     <li>**footer_left_{instantiated class name}_{page slug}** – [3.5.5+] receives an HTML output for the left footer.</li>
 *     <li>**footer_left_{instantiated class name}_{page slug}_{tab slug}** – [3.5.5+] receives an HTML output for the left footer.</li>
 *     <li>**footer_right_{instantiated class name}** – [3.5.5+] receives an HTML output for the right footer.</li>
 *     <li>**footer_right_{instantiated class name}_{page slug}** – [3.5.5+] receives an HTML output for the right footer.</li>
 *     <li>**footer_right_{instantiated class name}_{page slug}_{tab slug}** – [3.5.5+] receives an HTML output for the right footer.</li>
 * </ul>
 *
 * <h4>Remark</h4>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 *
 * <h4>Example</h4>
 * <p>If the instantiated class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>
 * class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_top_Sample_Admin_Pages( $sContent ) {
 *         return '<div style="float:right;"><img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /></div>'
 *             . $sContent;
 *     }
 * ...
 * }
 * </code>
 *
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>
 * class Sample_Admin_Pages extends AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $sContent ) {
 *         return $sContent . '<p>Hello world!</p>';
 *     }
 * ...
 * }</code>
 *
 * <h4>Timing of Hooks</h4>
 * <code>
 * ------ After the class is instantiated ------
 *
 *  start_{instantiated class name}
 *
 * ------ When the page starts loading  ------
 *
 *  load_{instantiated class name}
 *  load_{page slug}
 *  load_{page slug}_{tab slug}
 *  load_after_{instantiated class name}
 *
 *  sections_{instantiated class name}
 *  fields_{instantiated class name}
 *  pages_{instantiated class name}
 *  tabs_{instantiated class name}_{page slug}
 *
 *  options_update_status_{instantiated class name}
 *  options_update_status_{page slug}
 *  options_update_status_{page slug}_{tab slug}
 *  submit_{instantiated class name}_{pressed submit field id}
 *  submit_{instantiated class name}_{section id}
 *  submit_{instantiated class name}_{section id}_{field id}
 *  submit_{instantiated class name}_{page slug}
 *  submit_{instantiated class name}_{page slug}_{tab slug}
 *  submit_{instantiated class name}
 *  validation_saved_options_{instantiated class name}
 *  validation_saved_options_{page slug}_{tab slug}
 *  validation_saved_options_{page slug}
 *  validation_saved_options_without_dynamic_elements_{instantiated class name}
 *  validation_saved_options_without_dynamic_elements_{page slug}_{tab slug}
 *  validation_saved_options_without_dynamic_elements_{page slug}
 *  validation_{instantiated class name}_{field id (which does not have a section)}
 *  validation_{instantiated class name}_{section_id}
 *  validation_{instantiated class name}_{section id}_{field id}
 *  validation_{page slug}_{tab slug}
 *  validation_{page slug }
 *  validation_{instantiated class name }
 *  export_{page slug}_{tab slug}
 *  export_{page slug}
 *  export_{instantiated class name}
 *  import_{page slug}_{tab slug}
 *  import_{page slug}
 *  import_{instantiated class name}
 *
 *  ------ Start Rendering HTML - after HTML header is sent ------
 *
 *  <head>
 *      <style type="text/css" name="admin-page-framework">
 *          style_{page slug}_{tab slug}
 *          style_{page slug}
 *          style_{instantiated class name}
 *          script_{page slug}_{tab slug}
 *          script_{page slug}
 *          script_{instantiated class name}
 *      </style>
 *
 *  <head/>
 *
 *  do_before_{instantiated class name}
 *  do_before_{page slug}
 *  do_before_{page slug}_{tab slug}
 *
 *  <div class="wrap">
 *
 *      content_top_{page slug}_{tab slug}
 *      content_top_{page slug}
 *      content_top_{instantiated class name}
 *
 *      <div class="admin-page-framework-container">
 *          <form action="current page" method="post">
 *
 *              do_form_{instantiated class name}
 *              do_form_{page slug}
 *              do_form_{page slug}_{tab slug}
 *
 *              field_definition_{instantiated class name}_{section ID}_{field ID}
 *              field_definition_{instantiated class name}_{field ID (which does not have a section)}
 *              section_head_{instantiated class name}_{section ID}
 *              field_{instantiated class name}_{field ID}
 *
 *              content_{page slug}_{tab slug}
 *              content_{page slug}
 *              content_{instantiated class name}
 *
 *              do_{instantiated class name}
 *              do_{page slug}
 *              do_{page slug}_{tab slug}
 *
 *          </form>
 *      </div>
 *
 *          content_bottom_{page slug}_{tab slug}
 *          content_bottom_{page slug}
 *          content_bottom_{instantiated class name}
 *
 *  </div>
 *
 *  do_after_{instantiated class name}
 *  do_after_{page slug}
 *  do_after_{page slug}_{tab slug}
 *
 * </code>
 *
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/admin_page.png
 * @since       3.3.0
 * @package     AdminPageFramework/Factory/AdminPage
 * @heading     Admin Page
 * @remark      When you view the code of the code, most of the internal methods are prefixed with the underscore like `_getSomething()` and callback methods are prefixed with <code>_reply</code>.
 * The methods for the users are public and do not have those prefixes.
 */
class AdminPageFramework_Documentaiton {}
