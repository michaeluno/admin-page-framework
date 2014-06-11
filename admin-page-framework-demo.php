<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.1.0b05
	Requirements: PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Define constants for the demo plugin - these are not necessary in your project */
define( 'APFDEMO_DEVMODE', true );
define( 'APFDEMO_FILE', __FILE__ );
define( 'APFDEMO_DIRNAME', dirname( APFDEMO_FILE ) );

/* Include the library */
if ( ! class_exists( 'AdminPageFramework' ) ) {
	include_once( 
		defined( 'APFDEMO_DEVMODE' ) && APFDEMO_DEVMODE
			? APFDEMO_DIRNAME . '/development/admin-page-framework.php'	// use the development version when you need to debug.
			: APFDEMO_DIRNAME . '/library/admin-page-framework.min.php'	// use the minified version in your plugins or themes.
	);
}

if ( is_admin() ) :
	
 	// Create an example page group
	include_once( APFDEMO_DIRNAME . '/example/APF_BasicUsage.php' );	// Include the basic usage example that creates a root page and its sub-pages.
	new APF_BasicUsage;

	// Add pages and forms in the custom post type root page
	include_once( APFDEMO_DIRNAME . '/example/APF_Demo.php' );	// Include the demo class that creates various forms.
	new APF_Demo; 

	// Create meta boxes with form fields that appear in post definition pages (where you create a post) of the given post type.
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_BuiltinFieldTypes.php' );	
	new APF_MetaBox_BuiltinFieldTypes(
		'sample_custom_meta_box',	// meta box ID
		__( 'Demo Meta Box with Built-in Field Types', 'admin-page-framework-demo' ),	// title
		array( 'apf_posts' ),	// post type slugs: post, page, etc.
		'normal',	// context (what kind of metabox this is)
		'default'	// priority
	);
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_CustomFieldTypes.php' );	
	new APF_MetaBox_CustomFieldTypes(
		'sample_custom_meta_box_with_custom_field_types',	// meta box ID
		__( 'Demo Meta Box with Custom Field Types', 'admin-page-framework-demo' ),		// title
		array( 'apf_posts' ),	// post type slugs: post, page, etc.
		'normal',	// context
		'default'	// priority
	);
	
	// Create meta boxes in the pages added with the framework 
 	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Normal.php' );

	new APF_MetaBox_For_Pages_Normal(
		'apf_metabox_for_pages_normal',		// meta box id
		__( 'Sample Meta Box for Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ),	// title
		'apf_first_page',	// page slugs
		'normal',	// context
		'default'	// priority
	);
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
	new APF_MetaBox_For_Pages_Advanced(	
		'apf_metabox_for_pages_advanced',	// meta box id
		__( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ),	// title
		'apf_first_page',	// page slugs
		'advanced',		// context
		'default'	// priority
	);	
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );	
	new APF_MetaBox_For_Pages_Side(	
		'apf_metabox_for_pages_side',	// meta box id
		__( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ),	// title
		array( 'apf_first_page', 'apf_second_page' ),	// page slugs - setting multiple slugs is possible
		'side',		// context
		'default'	// priority
	);		  

	// Add fields in the taxonomy page
	include_once( APFDEMO_DIRNAME . '/example/APF_TaxonomyField.php' );
	new APF_TaxonomyField( 'apf_sample_taxonomy' );		// taxonomy slug
	
	if ( is_network_admin() ) :
	
		// Add pages and forms in the network admin area.
		include_once( APFDEMO_DIRNAME . '/example/APF_NetworkAdmin.php' );	// Include the demo class that creates various forms.
		new APF_NetworkAdmin; 	
		
		new APF_MetaBox_For_Pages_Side(	
			'apf_metabox_for_pages_side',	// meta box id
			__( 'Sample Meta Box for Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ),	// title
			array( 'apf_builtin_field_types' ),	// page slugs - setting multiple slugs is possible
			'side',		// context
			'default'	// priority
		);		  
		
		// new APF_MetaBox_For_Pages_Normal(
			// 'apf_metabox_for_pages_normal',		// meta box id
			// __( 'Sample Meta Box for Network Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ),	// title
			// 'apf_builtin_field_types',	// page slugs
			// 'normal',	// context
			// 'default'	// priority
		// );		
		
	endif;
	
endif;

// Creates a custom post type
include_once( APFDEMO_DIRNAME . '/example/APF_PostType.php' );
new APF_PostType( 	// this class deals with front-end components so checking with is_admin() is not necessary.
	'apf_posts', 	// post type slug - you can pass multiple slugs with an array e.g. array( 'apf_posts', 'post', 'page' )
	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		'labels' => array(
			'name' => 'Admin Page Framework',
			'all_items' => __( 'Sample Posts', 'admin-page-framework-demo' ),
			'singular_name' => 'Admin Page Framework',
			'add_new' => __( 'Add New', 'admin-page-framework-demo' ),
			'add_new_item' => __( 'Add New APF Post', 'admin-page-framework-demo' ),
			'edit' => __( 'Edit', 'admin-page-framework-demo' ),
			'edit_item' => __( 'Edit APF Post', 'admin-page-framework-demo' ),
			'new_item' => __( 'New APF Post', 'admin-page-framework-demo' ),
			'view' => __( 'View', 'admin-page-framework-demo' ),
			'view_item' => __( 'View APF Post', 'admin-page-framework-demo' ),
			'search_items' => __( 'Search APF Post', 'admin-page-framework-demo' ),
			'not_found' => __( 'No APF Post found', 'admin-page-framework-demo' ),
			'not_found_in_trash' => __( 'No APF Post found in Trash', 'admin-page-framework-demo' ),
			'parent' => __( 'Parent APF Post', 'admin-page-framework-demo' ),
			'plugin_listing_table_title_cell_link'	=>	__( 'APF Posts', 'admin-page-framework-demo' ),		// framework specific key. [3.0.6+]
		),
		'public' => true,
		'menu_position' => 110,
		'supports' => array( 'title' ), // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),	// 'custom-fields'
		'taxonomies' => array( '' ),
		'has_archive' => true,
		'show_admin_column' => true,	// this is for custom taxonomies to automatically add the column in the listing table.
		'menu_icon' => plugins_url( 'asset/image/wp-logo_16x16.png', APFDEMO_FILE ),
		// ( framework specific key ) this sets the screen icon for the post type for WordPress v3.7.1 or below.
		'screen_icon' => dirname( APFDEMO_FILE  ) . '/asset/image/wp-logo_32x32.png', // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
	)
);
	
/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 */