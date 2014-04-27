<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.0.5b03
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
			? APFDEMO_DIRNAME . '/development/admin-page-framework.php'
			: APFDEMO_DIRNAME . '/library/admin-page-framework.min.php'
	);
}

if ( is_admin() ) :
	
 	// Create an example page group
	include_once( APFDEMO_DIRNAME . '/example/APF_BasicUsage.php' );	// Include the basic usage example that creates a root page and its sub-pages.
	new APF_BasicUsage;

	// Adds pages and forms in the custom post type root page  - 2.2 seconds
	include_once( APFDEMO_DIRNAME . '/example/APF_Demo.php' );	// Include the demo class that creates various forms.
	new APF_Demo; 

	// Create a meta box with form fields
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
		'normal',	// context (what kind of metabox this is)
		'default'	// priority
	);
	
	// Create meta boxes in the pages added with the framework 
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Normal.php' );
	new APF_MetaBox_For_Pages_Normal(
		'apf_metabox_for_pages_normal',		// meta box id
		__( 'Sample Meta Box For Admin Pages Inserted in Normal Area', 'admin-page-framework-demo' ),	// title
		'apf_first_page',	// page slugs
		'normal',	// context
		'default'	// priority
	);
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Advanced.php' );
	new APF_MetaBox_For_Pages_Advanced(	
		'apf_metabox_for_pages_advanced',	// meta box id
		__( 'Sample Meta Box For Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ),	// title
		'apf_first_page',	// page slugs
		'advanced',		// context
		'default'	// priority
	);	
	include_once( APFDEMO_DIRNAME . '/example/APF_MetaBox_For_Pages_Side.php' );	
	new APF_MetaBox_For_Pages_Side(	
		'apf_metabox_for_pages_side',	// meta box id
		__( 'Sample Meta Box For Admin Pages Inserted in Advanced Area', 'admin-page-framework-demo' ),	// title
		array( 'apf_first_page', 'apf_second_page' ),	// page slugs - setting multiple slugs is possible
		'side',		// context
		'default'	// priority
	);		 

	// Add fields in the taxonomy page
	include_once( APFDEMO_DIRNAME . '/example/APF_TaxonomyField.php' );
	new APF_TaxonomyField( 'apf_sample_taxonomy' );		// taxonomy slug
	
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
			'add_new' => 'Add New',
			'add_new_item' => 'Add New APF Post',
			'edit' => 'Edit',
			'edit_item' => 'Edit APF Post',
			'new_item' => 'New APF Post',
			'view' => 'View',
			'view_item' => 'View APF Post',
			'search_items' => 'Search APF Post',
			'not_found' => 'No APF Post found',
			'not_found_in_trash' => 'No APF Post found in Trash',
			'parent' => 'Parent APF Post'
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