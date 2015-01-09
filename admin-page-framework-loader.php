<?php
/* 
    Plugin Name:    Admin Page Framework Loader
    Plugin URI:     http://en.michaeluno.jp/admin-page-framework
    Description:    Loads Admin Page Framework and some tools.
    Author:         Michael Uno
    Author URI:     http://michaeluno.jp
    Version:        3.5.0b03
    Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
*/ 

/**
 * The base class of the registry class which provides basic plugin information.
 * 
 * @since       3.5.0
 */
class AdminPageFrameworkLoader_Registry_Base {

	const Version        = '3.5.0';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name           = 'Admin Page Framework Loader';
	const Description    = 'Loads Admin Page Framework and some tools.';
	const URI            = 'http://en.michaeluno.jp/';
	const Author         = 'miunosoft (Michael Uno)';
	const AuthorURI      = 'http://en.michaeluno.jp/';
	const Copyright      = 'Copyright (c) 2015, Michael Uno';
	const License        = 'GPL v2 or later';
	const Contributors   = '';
	
}
/**
 * Provides plugin information.
 * 
 * The plugin will refer to these information.
 * 
 * @since       3.5.0
 * @remark      
 */
final class AdminPageFrameworkLoader_Registry extends AdminPageFrameworkLoader_Registry_Base {
	        
    /**
     * The plugin option key used for the options table.
     */
	const OptionKey                 = 'admin_page_framework_loader';
    
    /**
     * The transient prefix. 
     * 
     * @remark      This is also accessed from uninstall.php so do not remove.
     * @remark      Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
     */
	const TransientPrefix           = 'APFL_';
    
	const TextDomain                = 'admin-page-framework-loader';
	// const TextDomainPath            = './language';    
    
	// const AdminPage_ = '...';
	// const AdminPage_Root            = 'AdminPageFrameworkLoader_AdminPage';    // the root menu page slug
	// const AdminPage_Settings        = 'si_settings';    // the root menu page slug
	const AdminPage_Tool            = 'apfl_tool';    // the root menu page slug
    const AdminPage_Contact         = 'apfl_contact';
    
    // const PostType_ = '';
	// const PostType_ImageGroup       = 'cfi_image_group';        // up to 20 characters
    
	// const Taxonomy_ = '';
	const RequiredPHPVersion        = '5.2.4';
	const RequiredWordPressVersion  = '3.3';
	    
	// These properties will be defined in the setUp() method.
	static public $sFilePath = '';
	static public $sDirPath  = '';
	
	/**
	 * Sets up static properties.
	 */
	static function setUp( $sPluginFilePath=null ) {
	                    
		self::$sFilePath = $sPluginFilePath ? $sPluginFilePath : __FILE__;
		self::$sDirPath  = dirname( self::$sFilePath );
	    
	}    
	
	/**
	 * Returns the URL with the given relative path to the plugin path.
	 * 
	 * Example:  AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/css/meta_box.css' );
     * @since       3.5.0
	 */
	public static function getPluginURL( $sRelativePath='' ) {
		return plugins_url( $sRelativePath, self::$sFilePath );
	}
    
    /**
     * Returns the information of this class.
     * 
     * @since       3.5.0
     */
    static public function getInfo() {
        $_oReflection = new ReflectionClass( __CLASS__ );
        return $_oReflection->getConstants()
            + $_oReflection->getStaticProperties()
        ;
    }    
    
}
/* Registry set up. */
AdminPageFrameworkLoader_Registry::setUp( __FILE__ );

// Do no load if accessed directly - not exiting because the uninstall.php or inclusion list generator will load this file.
if ( ! defined( 'ABSPATH' ) ) { return; }
if ( ! defined( 'DOWING_UNINSTALL' ) ) { return; }

// Include the library file 
if ( class_exists( 'AdminPageFramework' ) ) {
    trigger_error( 
        AdminPageFrameworkLoader_Registry::Name . ' : ' . 'The framework is already included. This plugin will not load it to avoid library conflicts.',
        E_USER_ERROR 
    );
    return;    
}
include( 
    defined( 'WP_DEBUG' ) && WP_DEBUG
        ? dirname( __FILE__ ) . '/development/admin-page-framework.php' // use the development version when you need to do debugging.
        : dirname( __FILE__ ) . '/library/admin-page-framework.min.php' // use the minified version in your plugins or themes.
);


// Include the framework loader plugin pages extra components.
include( dirname( __FILE__ ) . '/include/class/boot/AdminPageFrameworkLoader_Bootstrap.php' );
new AdminPageFrameworkLoader_Bootstrap( __FILE__ );

/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 * 
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 * 
 * Happy coding!
 */