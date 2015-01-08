<?php
/* 
    Plugin Name:    Admin Page Framework
    Plugin URI:     http://en.michaeluno.jp/admin-page-framework
    Description:    Loads Admin Page Framework and some tools.
    Author:         Michael Uno
    Author URI:     http://michaeluno.jp
    Version:        3.5.0b03
    Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Include the library file 
if ( class_exists( 'AdminPageFramework' ) ) {
    
    // @todo Trigger a warning.
    
    return;
    
}
include( 
    defined( 'WP_DEBUG' ) && WP_DEBUG
        ? APFDEMO_DIRNAME . '/development/admin-page-framework.php' // use the development version when you need to do debugging.
        : APFDEMO_DIRNAME . '/library/admin-page-framework.min.php' // use the minified version in your plugins or themes.
);

// Include the framework loader plugin pages.
// @todo include bootstrap and admin pages.

/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 */