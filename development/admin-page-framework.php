<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2016 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

if ( ! class_exists( 'AdminPageFramework_Registry' ) ) :
/**
 * Facilitates WordPress plugin and theme development.
 *
 * @heading             Admin Page Framework
 * @author              Michael Uno <michael@michaeluno.jp>
 * @copyright           2013-2016 (c) Michael Uno
 * @license             http://opensource.org/licenses/MIT  MIT
 * @since               3.1.3
 * @repository          https://github.com/michaeluno/admin-page-framework
 * @link                http://wordpress.org/plugins/admin-page-framework/
 * @link                http://en.michaeluno.jp/admin-page-framework
 * @package             AdminPageFramework
 * @requirement         >= WordPress 3.4
 * @requirement         >= PHP 5.2.4
 * @remark              To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remark              The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @download_latest     https://github.com/michaeluno/admin-page-framework/archive/master.zip
 * @download_stable     http://downloads.wordpress.org/plugin/admin-page-framework.latest-stable.zip
 * @catchcopy           The framework for all WordPress developers.
 * @version             3.7.9b05
 */
abstract class AdminPageFramework_Registry_Base {

    const VERSION       = '3.7.9b05'; // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME          = 'Admin Page Framework';
    const DESCRIPTION   = 'Facilitates WordPress plugin and theme development.';
    const URI           = 'http://en.michaeluno.jp/admin-page-framework';
    const AUTHOR        = 'Michael Uno';
    const AUTHOR_URI    = 'http://en.michaeluno.jp/';
    const COPYRIGHT     = 'Copyright (c) 2013-2016, Michael Uno';
    const LICENSE       = 'MIT <http://opensource.org/licenses/MIT>';
    const CONTRIBUTORS  = '';

}

/**
 * Defines the framework common information.
 *
 * @since       3.1.3
 * @package     AdminPageFramework
 * @internal
 */
final class AdminPageFramework_Registry extends AdminPageFramework_Registry_Base {

    const TEXT_DOMAIN        = 'admin-page-framework';
    const TEXT_DOMAIN_PATH   = '/language';  // not used at the moment

    /**
     * Indicates whether the framework is loaded from the minified version or not.
     *
     * @remark      The value will be reassigned by the bootstrap script.
     * @remark      The minified version will be deprecated in the near future.
     */
    static public $bIsMinifiedVersion = true;

    /**
     * Indicates whether the framework is the development version or not.
     *
     * @since       3.5.4
     */
    static public $bIsDevelopmentVersion = true;

    /**
     * Stores the autoloader class file path.
     */
    static public $sAutoLoaderPath;

    /**
     * Stores the include class list file path.
     * @since       3.5.4
     */
    static public $sIncludeClassListPath;

    /**
     * Stores paths of class files.
     * @since       3.5.4
     */
    static public $aClassFiles = array();

    // These properties will be defined in the setUp() method.
    static public $sFilePath   = '';
    static public $sDirPath    = '';

    /**
     * Sets up static properties.
     * @return      void
     */
    static public function setUp( $sFilePath=__FILE__ ) {

        self::$sFilePath                = $sFilePath;
        self::$sDirPath                 = dirname( self::$sFilePath );
        self::$sIncludeClassListPath    = self::$sDirPath . '/admin-page-framework-include-class-list.php';
        self::$aClassFiles              = self::_getClassFilePathList( self::$sIncludeClassListPath );
        self::$sAutoLoaderPath          = isset( self::$aClassFiles[ 'AdminPageFramework_RegisterClasses' ] )
            ? self::$aClassFiles[ 'AdminPageFramework_RegisterClasses' ]
            : '';
        self::$bIsMinifiedVersion       = class_exists( 'AdminPageFramework_MinifiedVersionHeader' );
        self::$bIsDevelopmentVersion    = isset( self::$aClassFiles[ 'AdminPageFramework_InclusionClassFilesHeader' ] ) && file_exists(
            self::$aClassFiles[ 'AdminPageFramework_InclusionClassFilesHeader' ] 
        );
        
    }
        /**
         * Returns the class file path list.
         * @since       3.5.4
         * @return      array
         */
        static private function _getClassFilePathList( $sInclusionClassListPath ) {
            $aClassFiles = array();    // this will be updated if the inclusion below is successful.
            include( $sInclusionClassListPath );
            return $aClassFiles;
        }

    /**
     * Returns the framework version.
     *
     * @since       3.3.1
     * @return      string
     */
    static public function getVersion() {

        if ( ! isset( self::$sAutoLoaderPath ) ) {
            trigger_error( 'Admin Page Framework: ' . ' : ' . sprintf( __( 'The method is called too early. Perform <code>%2$s</code> earlier.', 'admin-page-framework' ), __METHOD__, 'setUp()' ), E_USER_WARNING );
            return self::VERSION;
        }
        $_aMinifiedVesionSuffix     = array(
            0 => '',
            1 => '.min',
        );
        $_aDevelopmentVersionSuffix = array(
            0 => '',
            1 => '.dev',
        );
        return self::VERSION
            . $_aMinifiedVesionSuffix[ ( integer ) self::$bIsMinifiedVersion ]
            . $_aDevelopmentVersionSuffix[ ( integer ) self::$bIsDevelopmentVersion ];

    }

    /**
     * Returns an information array of this class.
     *
     * @since       3.4.6
     * @return      array
     */
    static public function getInfo() {
        $_oReflection = new ReflectionClass( __CLASS__ );
        return $_oReflection->getConstants()
            + $_oReflection->getStaticProperties();
    }

}
endif;

if ( ! class_exists( 'AdminPageFramework_Bootstrap' ) ) :
/**
 * Loads the Admin Page Framework library.
 *
 * @since       3.0.0
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
final class AdminPageFramework_Bootstrap {

    /**
     * Indicates whether the bootstrap has run or not.
     */
    static private $_bLoaded = false;

    /**
     * Loads the framework.
     */
    public function __construct( $sLibraryPath=__FILE__ ) {

        if ( ! $this->_isLoadable() ) {
            return;
        }

        // Sets up registry properties.
        AdminPageFramework_Registry::setUp( $sLibraryPath );

        // Bail if it is the minified version.
        if ( AdminPageFramework_Registry::$bIsMinifiedVersion ) {
            return;
        }

        // Load the classes only for the non-minified version.
        $this->_include();

    }
        /**
         * Checks whether the framework can be loaded or not.
         *
         * @since       3.5.4
         * @return      boolean
         */
        private function _isLoadable() {

            // Prevent it from being loaded multiple times.
            if ( self::$_bLoaded ) {
                return false;
            }
            self::$_bLoaded = true;

            // The minifier script will include this file (but it does not include WordPress) to use the reflection class to extract the doc-block.
            return defined( 'ABSPATH' );

        }
        
        /**
         * Includes required files and registers auto-load classes.
         * 
         * @since       3.7.3
         * @return      void
         */
        private function _include() {
            
            include( AdminPageFramework_Registry::$sAutoLoaderPath );
            new AdminPageFramework_RegisterClasses(
                // the scanning directory
                empty( AdminPageFramework_Registry::$aClassFiles )
                    ? AdminPageFramework_Registry::$sDirPath
                    : '',
                // search options
                array(
                    'exclude_class_names'   => array(
                        'AdminPageFramework_MinifiedVersionHeader',
                        'AdminPageFramework_BeautifiedVersionHeader',
                    ),
                ),
                // a class list array
                AdminPageFramework_Registry::$aClassFiles
            );            
             
            /**
             * Reduce the nesting level of recursive function calls produced by the `spl_autoload_call()` PHP function.
             * 
             * Instantiating a class with many class inheritances (extending a class) triggers `spl_autoload_call()` 
             * and it keeps getting called until all the parent classes are loaded, which causes a fatal error,
             * `Maximum function nesting level of 'x' reached,..` if the Xdebug extension is enabled with a low value of the `xdebug.max_nesting_level` option.
             * 
             * This instantiation of a class below won't do anything in particular but just tells the spl autoloader to include those files 
             * so that the next time the program utilizing the framework tries to instantiate its class has a less nesting level of nested function calls,
             * which reduces the chance of getting the fatal error.
             */
            if ( extension_loaded( 'xdebug' ) ) {
                new AdminPageFramework_Utility;
                new AdminPageFramework_WPUtility;
            }            
            
        }
        
}
new AdminPageFramework_Bootstrap( __FILE__ );
endif;