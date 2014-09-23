<?php 
/**
 * Admin Page Framework
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes.
 * 
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2014 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @see         http://wordpress.org/plugins/admin-page-framework/
 * @see         https://github.com/michaeluno/admin-page-framework
 * @link        http://en.michaeluno.jp/admin-page-framework
 * @package     AdminPageFramework
 * @remarks     To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remarks     Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * @remarks     The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @version     3.2.0b06
 */
if ( ! class_exists( 'AdminPageFramework_Registry_Base' ) ) :
abstract class AdminPageFramework_Registry_Base {
    
    const Version       = '3.2.0b06'; // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const Name          = 'Admin Page Framework';
    const Description   = 'Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes.';
    const URI           = 'http://en.michaeluno.jp/admin-page-framework';
    const Author        = 'Michael Uno';
    const AuthorURI     = 'http://en.michaeluno.jp/';
    const Copyright     = 'Copyright (c) 2013-2014, Michael Uno';
    const License       = 'MIT <http://opensource.org/licenses/MIT>';
    const Contributors  = '';    
    
}
endif;
if ( ! class_exists( 'AdminPageFramework_Registry' ) ) :
/**
 * Defines the framework common information.
 * 
 * @since 3.1.3
 */
final class AdminPageFramework_Registry extends AdminPageFramework_Registry_Base {
        
    const TextDomain        = 'admin-page-framework';
    const TextDomainPath    = './language';
    
    /**
     * Indicates whether the framework is loaded from the minified version or not.
     * 
     * @remark The value will be reassign by the bootstrap script.
     */
    static public $bIsMinifiedVersion = true;
    
    /**
     * Stores the autoloader class file path.
     */
    static public $sAutoLoaderPath;
    
    // These properties will be defined in the setUp() method.
    static public $sFilePath    = '';
    static public $sDirPath     = '';
    static public $sFileURI     = '';
    
    /**
     * Sets up static properties.
     */
    static function setUp( $sFilePath=null ) {
                        
        self::$sFilePath            = $sFilePath ? $sFilePath : __FILE__;
        self::$sDirPath             = dirname( self::$sFilePath );
        self::$sFileURI             = plugins_url( '', self::$sFilePath );
        self::$sAutoLoaderPath      = self::$sDirPath . '/utility/AdminPageFramework_RegisterClasses.php';
        self::$bIsMinifiedVersion   = ! file_exists( self::$sAutoLoaderPath );
        
    }    
    
}
endif;

if ( ! class_exists( 'AdminPageFramework_Bootstrap' ) ) :
/**
 * Loads the Admin Page Framework library.
 * 
 * @copyright   2013-2014 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @see         http://wordpress.org/plugins/admin-page-framework/
 * @see         https://github.com/michaeluno/admin-page-framework
 * @link        http://en.michaeluno.jp/admin-page-framework
 * @since       3.0.0
 * @package     AdminPageFramework
 * @subpackage  Utility
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
        
        // Sets up registry properties.
        AdminPageFramework_Registry::setUp( $sLibraryPath );
            
        // Load the classes. For the minified version, the autoloader class should not be located in the utility folder.
        if ( ! AdminPageFramework_Registry::$bIsMinifiedVersion ) {
            include( AdminPageFramework_Registry::$sAutoLoaderPath );     
            include( AdminPageFramework_Registry::$sDirPath . '/admin-page-framework-include-class-list.php' );
            new AdminPageFramework_RegisterClasses( 
                isset( $aClassFiles ) ? '' : AdminPageFramework_Registry::$sDirPath,     // scanning directory
                array(), // search options
                isset( $aClassFiles ) ? $aClassFiles : array() // default class list array
            );
        }
        
    }
    
}
new AdminPageFramework_Bootstrap( __FILE__ ); // do it now
endif;