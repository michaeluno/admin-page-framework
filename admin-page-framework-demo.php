<?php
/* 
	Plugin Name: Admin Page Framework - Demo
	Plugin URI: http://en.michaeluno.jp/admin-page-framework
	Description: Demonstrates the features of the Admin Page Framework class.
	Author: Michael Uno
	Author URI: http://michaeluno.jp
	Version: 3.0.0b
	Requirements: PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

define( 'APFDEMO_FILE', __FILE__ );

// Include the library
if ( ! class_exists( 'AdminPageFramework' ) )
    include_once( dirname( APFDEMO_FILE ) . '/class/admin-page-framework.php' );

// Include the basic usage example that creates a root page and its sub-pages.
include_once( dirname( APFDEMO_FILE ) . '/example/APF_BasicUsage.php' );

// Include the demo class that creates various forms.
include_once( dirname( APFDEMO_FILE ) . '/example/APF_Demo.php' );

// Include the demo class that creates a custom post type. 
include_once( dirname( APFDEMO_FILE ) . '/example/APF_PostType.php' );

// Include the demo class that creates a meta box.
include_once( dirname( APFDEMO_FILE ) . '/example/APF_MetaBox.php' );
	
/*
 * 
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 * 
 */