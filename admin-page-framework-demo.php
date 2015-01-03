<?php
/* 
    Plugin Name:    Admin Page Framework - Demo
    Plugin URI:     http://en.michaeluno.jp/admin-page-framework
    Description:    Demonstrates the features of the Admin Page Framework class.
    Author:         Michael Uno
    Author URI:     http://michaeluno.jp
    Version:        3.4.6b11
    Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Define constants for the demo plugin - these are not necessary in your project */
define( 'APFDEMO_FILE', __FILE__ );
define( 'APFDEMO_DIRNAME', dirname( APFDEMO_FILE ) );

/* Localization */		
if ( is_admin() ) {
    load_plugin_textdomain( 
        'admin-page-framework-demo', // text domain
        false,   // deprecated
        basename( APFDEMO_DIRNAME ) . '/language/'  // Relative path to WP_PLUGIN_DIR where the .mo file resides.
    );
    load_plugin_textdomain( 
        'admin-page-framework',     // text domain
        false,  // deprecated
        basename( APFDEMO_DIRNAME ) . '/language/'  // Relative path to WP_PLUGIN_DIR where the .mo file resides.
    );		
}

/* Include the library file */
if ( ! class_exists( 'AdminPageFramework' ) ) {
    $_sMinifiedVersionPath    = APFDEMO_DIRNAME . '/library/admin-page-framework-min.php';
    $_bIsDebugMode            = defined( 'WP_DEBUG' ) && WP_DEBUG;
    include( 
        ! $_bIsDebugMode && file_exists( $_sMinifiedVersionPath )
            ? $_sMinifiedVersionPath // use the minified version in your plugins or themes.
            : APFDEMO_DIRNAME . '/development/admin-page-framework.php' // use the development version when you need to do debugging.
    );
}

/* Load examples */
include( APFDEMO_DIRNAME . '/example/example-bootstrap.php' );

/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 */