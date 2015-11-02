<?php
/**
 * Admin Page Framework
 *
 * Facilitates WordPress plugin and theme development.
 *
 * @author      Michael Uno <michael@michaeluno.jp>
 * @copyright   2013-2015 (c) Michael Uno
 * @license     MIT <http://opensource.org/licenses/MIT>
 * @package     AdminPageFramework
 */

/**
 * Facilitates WordPress plugin and theme development.
 *
 * @heading             Admin Page Framework
 * @author              Michael Uno <michael@michaeluno.jp>
 * @copyright           2013-2015 (c) Michael Uno
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
 * @version             3.6.3b02
 */
abstract class AdminPageFramework_Registry_Base {

    const VERSION       = '3.6.3b02'; // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME          = 'Admin Page Framework';
    const DESCRIPTION   = 'Facilitates WordPress plugin and theme development.';
    const URI           = 'http://en.michaeluno.jp/admin-page-framework';
    const AUTHOR        = 'Michael Uno';
    const AUTHOR_URI    = 'http://en.michaeluno.jp/';
    const COPYRIGHT     = 'Copyright (c) 2013-2015, Michael Uno';
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
    static public $sFilePath    = '';
    static public $sDirPath     = '';
    static public $sFileURI     = '';

    /**
     * Sets up static properties.
     * @return      void
     */
    static public function setUp( $sFilePath=__FILE__ ) {

        self::$sFilePath                = $sFilePath;
        self::$sDirPath                 = dirname( self::$sFilePath );
        self::$sFileURI                 = plugins_url( '', self::$sFilePath );
        self::$sIncludeClassListPath    = self::$sDirPath . '/admin-page-framework-include-class-list.php';
        self::$aClassFiles              = self::_getClassFilePathList( self::$sIncludeClassListPath );
        self::$sAutoLoaderPath          = isset( self::$aClassFiles[ 'AdminPageFramework_RegisterClasses' ] )
            ? self::$aClassFiles[ 'AdminPageFramework_RegisterClasses' ]
            : '';
        self::$bIsMinifiedVersion       = class_exists( 'AdminPageFramework_MinifiedVersionHeader' );

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
        $_aMinifiedVesionSuffix = array(
            0 => '',
            1 => '.min',
        );
        $_aDevelopmentVersionSuffix = array(
            0 => '',
            1 => '.dev',
        );
        return self::VERSION
            . $_aMinifiedVesionSuffix[ ( int ) self::$bIsMinifiedVersion ]
            . $_aDevelopmentVersionSuffix[ ( int ) self::$bIsDevelopmentVersion ]
        ;

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
            + $_oReflection->getStaticProperties()
        ;
    }

}

/**
 * Loads the Admin Page Framework library.
 *
 * @copyright   2013-2015 (c) Michael Uno
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

        // Update a property - this must be done after registering classes.
        AdminPageFramework_Registry::$bIsDevelopmentVersion = class_exists( 'AdminPageFramework_InclusionClassFilesHeader' );

    }
        /**
         * Checks whether the framework can be loaded or not.
         *
         * @since       3.5.4
         * @return      boolean
         */
        private function _isLoadable() {

            // Prevent it from being loaded multiple times.
            if ( isset( self::$sAutoLoaderPath ) ) {
                return false;
            }

            // The minifier script will include this file ( but it does not include WordPress ) to use the reflection class to extract the docblock
            return defined( 'ABSPATH' );

        }

}
new AdminPageFramework_Bootstrap( __FILE__ );