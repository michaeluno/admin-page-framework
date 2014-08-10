<?php 
/**
 * Admin Page Framework
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes.
 * 
 * @author				Michael Uno <michael@michaeluno.jp>
 * @copyright			2013-2014 (c) Michael Uno
 * @license				MIT	<http://opensource.org/licenses/MIT>
 * @see				  	http://wordpress.org/plugins/admin-page-framework/
 * @see				  	https://github.com/michaeluno/admin-page-framework
 * @link			  	http://en.michaeluno.jp/admin-page-framework
 * @package				AdminPageFramework
 * @remarks				To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remarks				Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * @remarks				The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @version				3.1.3b03
 */

if ( ! class_exists( 'AdminPageFramework_Bootstrap' ) ) :
/**
 * Loads the Admin Page Framework library.
 * 
 * @info
 * Library Name: Admin Page Framework
 * Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
 * Author:  Michael Uno
 * Author URI: http://michaeluno.jp
 * Version: 3.1.3b03
 * Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * Description: Provides simpler means of building administration pages for plugin and theme developers.
 * @copyright	  	2013-2014 (c) Michael Uno
 * @license		  	MIT <http://opensource.org/licenses/MIT>
 * @see			    http://wordpress.org/plugins/admin-page-framework/
 * @see			    https://github.com/michaeluno/admin-page-framework
 * @link		    http://en.michaeluno.jp/admin-page-framework
 * @since		  	3.0.0
 * @remark			The minifier script will refer this comment section to create the comment header. So don't remove the @info section.
 * @remark			This class will not be included in the minifiled version.
 * @package			AdminPageFramework
 * @subpackage		Utility
 * @internal
 */
final class AdminPageFramework_Bootstrap {
	
	function __construct( $sLibraryPath ) {
		
		// The minifier script will include this file ( but it does not include WordPress ) to use the reflection class to extract the docblock
		if ( ! defined( 'ABSPATH' ) ) {
			return; 
		}
		
		// If the autoloader class exists, it means the framework has been loaded somewhere else.
		if ( class_exists( 'AdminPageFramework_RegisterClasses' ) ) {
			return;
		}
		
		// Load the classes. For the minified version, the autoloader class should not be located in the utility folder.
		$_sDirPath				= dirname( $sLibraryPath );
		$_sAutoLoaderClassPath	= $_sDirPath . '/utility/AdminPageFramework_RegisterClasses.php';
		if ( file_exists( $_sAutoLoaderClassPath ) ) {
			include( $_sAutoLoaderClassPath );
			new AdminPageFramework_RegisterClasses( $_sDirPath );
		}
		
	}
	
}
new AdminPageFramework_Bootstrap( __FILE__ );	// do it now
endif;

if ( ! class_exists( 'AdminPageFramework_Registry' ) ) :
abstract class AdminPageFramework_Registry_Base {
	
	const Version		= '3.1.3b03';	// <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name			= 'Admin Page Framework';
	const Description	= 'Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes.';
	const URI			= 'http://en.michaeluno.jp/admin-page-framework';
	const Author		= 'Michael Uno';
	const AuthorURI		= 'http://en.michaeluno.jp/';
	const Copyright		= 'Copyright (c) 2013-2014, Michael Uno';
	const License		= 'MIT	<http://opensource.org/licenses/MIT>';
	const Contributors	= '';	
}
endif;
if ( ! class_exists( 'AdminPageFramework_Registry' ) ) :
/**
 * Defines the framework common information.
 */
final class AdminPageFramework_Registry extends AdminPageFramework_Registry_Base {
		
}
endif;