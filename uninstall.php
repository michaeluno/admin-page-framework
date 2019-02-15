<?php
/**
 * Cleans up the plugin options.
 *    
 * @package      Admin Page Framework Loader
 * @copyright    Copyright (c) 2013-2019, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    return;
}
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    return;
}

/* 
 * Plugin specific constant. 
 * We are going to load the main file to get the registry class. And in the main file, 
 * if this constant is set, it will return after declaring the registry class.
 **/
if ( ! defined( 'DOING_UNINSTALL' ) ) {
    define( 'DOING_UNINSTALL', true  );
}

/**
 * Set the main plugin file name here.
 */
$_sMaingPluginFileName  = 'admin-page-framework-loader.php';
if ( file_exists( dirname( __FILE__ ). '/' . $_sMaingPluginFileName ) ) {
   include( $_sMaingPluginFileName );
}

if ( class_exists( 'AdminPageFrameworkLoader_Registry' ) ) :

    // Delete the plugin option    
    array_walk_recursive( AdminPageFrameworkLoader_Registry::$aOptionKeys, 'delete_option' );
    
    // Delete transients
    $_aPrefixes = array(
        AdminPageFrameworkLoader_Registry::TRANSIENT_PREFIX, // the plugin transients
        'apf_',      // the admin page framework transients
    );
    $_oWPDB = ${ 'GLOBALS' }[ 'wpdb' ];
    foreach( $_aPrefixes as $_sPrefix ) {
        if ( ! $_sPrefix ) { 
            continue; 
        }
        $_oWPDB->query( "DELETE FROM `{$_oWPDB->prefix}options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
        $_oWPDB->query( "DELETE FROM `{$_oWPDB->prefix}options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );
    }
    
endif;
