<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base to create admin pages in the network administration area.
 * 
 * <p>The functionality is basically the same as the admin page factory class ({@link AdminPageFramework}) except it serves in the network administration area.</p>
 * 
 * <h2>Creating an Admin Page in the Network Admin Area</h2>
 * 
 * 1. Define your own class by extending the {@link AdminPageFramework_NetworkAdmin} class.
 * 2. Set a top-level menu with the {@link AdminPageFramework_Controller_Menu::setRootMenuPage()} method.
 * 3. Add page items with the {@link AdminPageFramework_Controller_Menu::addSubMenuItems()} method.
 * 4. To insert contents, use the {@link AdminPageFramework::content()} method and return custom outputs.
 * 
 * For details of method parameters, see the links of the methods.
 * 
 * <h3>Example</h3>
 * <code>
 *  class APFDoc_CreateNetworkAdminPages extends AdminPageFramework_NetworkAdmin {
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
 *  new APFDoc_CreateNetworkAdminPages;
 * </code>
 * 
 * <h2>Adding In-page Tabs in Network Admin Pages</h2>
 * Use the {@link AdminPageFramework_Controller_Page::addInPageTabs()} method. For examples, see {@link AdminPageFramework}.
 * 
 * <h2>Creating Forms in Network Admin Pages</h2>
 * Use the {@link AdminPageFramework_Factory_Controller::addSettingField()} to register fields and {@link AdminPageFramework_Factory_Controller::addSettingSections()} methods for sections. For examples, see {@link AdminPageFramework}.
 * 
 * <h2>Retrieving Form Data of Network Admin Pages</h2>
 * Use the [get_site_option()](https://codex.wordpress.org/Function_Reference/get_site_option) by passing the option key. The option key is by default the extended class name unless you set your own key in the first parameter of the constructor.
 * To extract values from the returned multi-dimensional array, you may want to use the `AdminPageFramework_Utility::getElement()` method.
 * 
 * <h3>Example</h3>
 * <code>
 * $_aData         = get_site_option( 'APFDoc_CreateNetworkAdminPages', array() );
 * $_oUtil         = new AdminPageFramework_Utility;
 * $_sMyFieldValue = $_oUtil->getElement( 
 *      $_aData,    // subject array
 *      array( 'my_section_id', 'my_field_id' ),    // dimensional path
 *      'my default value'         // default value
 * );
 * </code>
 * 
 * <h2>Hooks</h2>
 * The same hooks of {@link AdminPageFramework} are used.
 * 
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/network_admin.png
 * @since       3.3.0
 * @extends     AdminPageFramework
 * @package     AdminPageFramework/Factory/NetworkAdmin
 * @heading     Network Admin Page
 */
abstract class AdminPageFramework_NetworkAdmin_Documentation extends AdminPageFramework_Documentaiton{}
