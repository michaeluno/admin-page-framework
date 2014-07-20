<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.1.1b05
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
			? APFDEMO_DIRNAME . '/development/admin-page-framework.php'	// use the development version when you need to do debugging.
			: APFDEMO_DIRNAME . '/library/admin-page-framework.min.php'	// use the minified version in your plugins or themes.
	);
}

/* Examples */

// Create a custom post type - this class deals with front-end components so checking with is_admin() is not necessary.
include_once( APFDEMO_DIRNAME . '/example/APF_PostType.php' );
new APF_PostType( 'apf_posts' ); 	// post type slug
if ( is_admin() ) :

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
	
	// Add fields in the taxonomy page
	include_once( APFDEMO_DIRNAME . '/example/APF_TaxonomyField.php' );
	new APF_TaxonomyField( 'apf_sample_taxonomy' );		// taxonomy slug

endif;


if ( is_admin() ) :
	
 	// Create an example page group and add sub-pages including a page with the slug 'apf_first_page'.
	include_once( APFDEMO_DIRNAME . '/example/APF_BasicUsage.php' );	// Include the basic usage example that creates a root page and its sub-pages.
	new APF_BasicUsage;

		// Create meta boxes that belongs to the 'apf_first_page' page.
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
	
	
	// Add pages and forms in the custom post type root page - all the settings should be defined in the setUp() method in each class.
	include_once( APFDEMO_DIRNAME . '/example/APF_Demo.php' );	// Include the demo class that creates various forms.
	new APF_Demo;

		// Add pages and forms in the custom post type root page
		include_once( APFDEMO_DIRNAME . '/example/APF_Demo_CustomFieldTypes.php' );	// Include the demo class that creates various forms.
		new APF_Demo_CustomFieldTypes( 'APF_Demo' );	// passing the option key used by the main pages.
			
		// Add the Manage Options page.
		include_once( APFDEMO_DIRNAME . '/example/APF_Demo_ManageOptions.php' );
		new APF_Demo_ManageOptions( 'APF_Demo' );	// passing the option key used by the main pages.
		
		// Add a hidden page.
		include_once( APFDEMO_DIRNAME . '/example/APF_Demo_HiddenPage.php' );
		new APF_Demo_HiddenPage;
		
		// Add the readme and the documentation sub-menu items to the above main demo plugin root page.
		include_once( APFDEMO_DIRNAME . '/example/APF_Demo_Readme.php' );
		new APF_Demo_Readme; 	 
			
			
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
		
	endif;
	
endif;

/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 */