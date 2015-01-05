<?php 
/**
 * Admin Page Framework
 * 
 * Facilitates WordPress plugin and theme development.
 * 
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2014 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

/**
 * Facilitates WordPress plugin and theme development.
 * 
 * @heading             Admin Page Framework
 * @author              Michael Uno <michael@michaeluno.jp>
 * @copyright           2013-2014 (c) Michael Uno
 * @license             http://opensource.org/licenses/MIT  MIT
 * @since               3.1.3
 * @repository          https://github.com/michaeluno/admin-page-framework
 * @link                http://wordpress.org/plugins/admin-page-framework/
 * @link                http://en.michaeluno.jp/admin-page-framework
 * @package             AdminPageFramework
 * @requirement         >= WordPress 3.3
 * @requirement         >= PHP 5.2.4
 * @remark              To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remark              The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @download_latest     https://github.com/michaeluno/admin-page-framework/archive/master.zip
 * @download_stable     http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip
 * @catchcopy           The framework for all WordPress developers.
 * @version             3.4.6
 */
abstract class AdminPageFramework_Registry_Base {
    
    const Version       = '3.4.6'; // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const Name          = 'Admin Page Framework';
    const Description   = 'Facilitates WordPress plugin and theme development.';
    const URI           = 'http://en.michaeluno.jp/admin-page-framework';
    const Author        = 'Michael Uno';
    const AuthorURI     = 'http://en.michaeluno.jp/';
    const Copyright     = 'Copyright (c) 2013-2014, Michael Uno';
    const License       = 'MIT <http://opensource.org/licenses/MIT>';
    const Contributors  = '';    
    
}

/**
 * Defines the framework common information.
 * 
 * @since       3.1.3
 * @package     AdminPageFramework
 * @internal
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
    
    /**
     * Returns the framework version.
     * 
     * @since       3.3.1
     */
    static function getVersion() {
        
        if ( ! isset( self::$sAutoLoaderPath ) ) {
            trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is called too early. Perform <code>%2$s</code> earlier.', 'admin-page-framework' ), __METHOD__, 'setUp()' ), E_USER_WARNING );
            return self::Version;
        }
        return self::Version 
            . ( self::$bIsMinifiedVersion ? '.min' : '' );        
            
    }
    
    /**
     * Returns the information of this class.
     * 
     * @since       3.4.6
     */
    static public function getInfo() {
        $_oReflection = new ReflectionClass( __CLASS__ );
        return $_oReflection->getConstants()
            + $_oReflection->getStaticProperties()
        ;
    }     
    
}

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
        // [3.4.6+] Deprecated as the minified version does not have if ( class_exists( )  checks any more so all the classes get loaded.
        // if ( class_exists( 'AdminPageFramework_RegisterClasses' ) ) {
            // return;
        // }
        
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