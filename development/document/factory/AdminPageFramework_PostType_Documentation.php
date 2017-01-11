<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides an abstract base class to create custom post types and taxonomies.
 * 
 * The user defines a class by extending the {@link AdminPageFramework_PostType} class. By instantiating the extended class, a new post type will be created.
 * 
 * <h2>Creating a Custom Post Type</h2>
 * 1. Define a class by extending the {@link AdminPageFramework_PostType} class.
 * 2. Set post type arguments by using the {@link AdminPageFramework_PostType_Controller::setArguments()} in the {@link AdminPageFramework_MetaBox_Controller::setUp()} method or pass the post type argument array to {@link AdminPageFramework_PostType::__construct()}.
 * 
 * <h2>Creating a Custom Taxonomy</h2>
 * In the {@link AdminPageFramework_MetaBox_Controller::setUp()} method, use the {@link AdminPageFramework_PostType_Controller::addTaxonomy()} method.
 * 
 * <h2>Example</h2>
 * <code>
 * class APFDoc_ExamplePostType extends AdminPageFramework_PostType {
 *
 *     public function setUp() {
 *  
 *         $this->setArguments(
 *             array( // argument - for the arguments, see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
 *                 'labels' => array(
 *                     'name'               => 'Tutorial Example',
 *                     'add_new_item'       => __( 'Example Post', 'admin-page-framework-tutorial' ),
 *                     'plugin_listing_table_title_cell_link' => __( 'Tutorial Example Custom Post Type', 'admin-page-framework-tutorial' ), // (framework specific key). [3.0.6+]
 *                 ),
 *                 'supports'          => array( 'title', 'editor' ), // e.g. array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
 *                 'public'            => true,
 *                 'menu_icon'         => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
 *                     ? 'dashicons-wordpress' 
 *                     : plugins_url( 'asset/image/wp-logo_16x16.png', APFDEMO_FILE ),
 *                 // (framework specific argument) this sets the screen icon for the post type for WordPress v3.7.1 or below.
 *                 'screen_icon' => dirname( APFDEMO_FILE  ) . '/asset/image/wp-logo_32x32.png', // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
 *             )    
 *         );    
 *  
 *         $this->addTaxonomy( 
 *             'apf_doc_example_taxonomy',  // taxonomy slug
 *             array(                  // argument - for arguments, see http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
 *                 'labels'                => array(
 *                     'name'          => __( 'Tutorial Taxonomy', 'admin-page-framework-tutorial' ),
 *                     'add_new_item'  => __( 'Add New Taxonomy', 'admin-page-framework-tutorial' ),
 *                     'new_item_name' => __( 'New Taxonomy', 'admin-page-framework-tutorial' )
 *                 ),
 *                 'show_ui'               => true,
 *                 'show_tagcloud'         => false,
 *                 'hierarchical'          => true,
 *                 'show_admin_column'     => true,
 *                 'show_in_nav_menus'     => true,
 *                 'show_table_filter'     => true,    // framework specific argument
 *                 'show_in_sidebar_menus' => true,    // framework specific argument
 *             )
 *         );
 *  
 *     }
 *     
 * }
 * new APFDoc_ExamplePostType( 'my_apf_post_type' ); 
 * </code>
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to do additional tasks such as setting a transient or modifying the data. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Action Hooks</h3>
 * <ul>
 *     <li>**start_{instantiated class name}** – triggered at the end of the class constructor. This receives the class object in the first parameter.</li>
 *     <li>**set_up_{instantiated class name}** – triggered after the setUp() method is called. This receives the class object in the first parameter.</li>
 * </ul>
 * <h3>Filter Hooks</h3>
 * <ul>
 *     <li>**cell_{post type slug}_{column key}** – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 *     <li>**columns_{post type slug}** – receives the array containing the header columns for the listing table of the custom post type's post. The first parameter: the header columns container array.</li>
 *     <li>**sortable_columns_{post type slug}** – receives the array containing the sortable header column array for the listing table of the custom post type's post. The first parameter: the sortable header columns container array.</li>
 *     <li>**footer_right_{instantiated class name}** – [3.5.5+] receives an HTML output for the right footer.</li> 
 *     <li>**footer_left_{instantiated class name}** – [3.5.5+] receives an HTML output for the left footer.</li> 
 *     <li>**action_links_{post type slug}** – [3.7.3+] receives an array of action links of the post listing table output. The second parameter: post object.</li> 
 * </ul>
 * 
 * <h3>Remark</h3>
 * <p>The post type factory class does not have the ability to create forms. Therefore, some common hooks such as `validation_{...}` and `options_{...}` are not available.</p> 
 * 
 * @image       http://admin-page-framework.michaeluno.jp/image/factory/post_type.png
 * @since       3.3.0
 * @package     AdminPageFramework/Factory/PostType
 * @heading     Post Type
 */
abstract class AdminPageFramework_PostType_Documentation {}
