<?php
/**
 *  Plugin Name:    Admin Page Framework - Loader
 *  Plugin URI:     http://admin-page-framework.michaeluno.jp/
 *  Description:    Loads Admin Page Framework which facilitates WordPress plugin and theme development.
 *  Author:         Michael Uno
 *  Author URI:     http://en.michaeluno.jp/
 *  Requirements:   PHP 5.2.4 or above, WordPress 3.3 or above.
 *  Version:        3.8.19
 */

/**
 * The base registry information.
 *
 * @since       3.5.0
 */
class AdminPageFrameworkLoader_Registry_Base {

    const VERSION        = '3.8.19';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
    const NAME           = 'Admin Page Framework - Loader'; // the name is not 'Admin Page Framework' because warning messages gets confusing.
    const SHORTNAME      = 'Admin Page Framework';  // used for a menu title etc.
    const DESCRIPTION    = 'Loads Admin Page Framework which facilitates WordPress plugin and theme development.';
    const URI            = 'http://admin-page-framework.michaeluno.jp/';
    const AUTHOR         = 'miunosoft (Michael Uno)';
    const AUTHOR_URI     = 'http://en.michaeluno.jp/';
    const COPYRIGHT      = 'Copyright (c) 2013-2019, Michael Uno';
    const LICENSE        = 'GPL v2 or later';
    const CONTRIBUTORS   = '';

}
/**
 * Provides the plugin information.
 *
 * The plugin will refer to these information.
 *
 * @since       3.5.0
 */
final class AdminPageFrameworkLoader_Registry extends AdminPageFrameworkLoader_Registry_Base {

    /**
     * The plugin option key used for the options table.
     */
    static public $aOptionKeys = array(
        'main'    => 'admin_page_framework_loader',
        'demo'    => array(
            'main'          => 'APF_Demo',
            'taxonomy'      => 'APF_TaxonomyField',
            'basic_usage'   => 'APF_BasicUsage',
        )
    );

    /**
     * The transient prefix.
     *
     * @remark      This is also accessed from `uninstall.php` so do not remove.
     * @remark      Do not exceed 8 characters as a transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added.
     */
    const TRANSIENT_PREFIX         = 'APFL_';

    /**
     * The hook slug used for the prefix of action and filter hook names.
     *
     * @remark      The ending underscore is not necessary.
     */
    const HOOK_SLUG                = 'admin_page_framework_loader';

    /**
     * The text domain slug and its path.
     *
     * These will be accessed from the bootstrap script.
     */
    const TEXT_DOMAIN              = 'admin-page-framework-loader';
    const TEXT_DOMAIN_PATH         = '/language';

    // These properties will be defined in the setUp() method.
    static public $sFilePath = '';
    static public $sDirPath  = '';

    /**
     * Requirements.
     */
    static public $aRequirements = array(
        'php' => array(
            'version'   => '5.2.4',
            'error'     => 'The plugin requires the PHP version %1$s or higher.',
        ),
        'wordpress'         => array(
            'version'   => '3.4',
            'error'     => 'The plugin requires the WordPress version %1$s or higher.',
        ),
        'mysql'             => array(
            'version'   => '5.0',
            'error'     => 'The plugin requires the MySQL version %1$s or higher.',
        ),
        'functions'         => '', // disabled
        // array(
            // e.g. 'mblang' => 'The plugin requires the mbstring extension.',
        // ),
        'classes'           => '', // disabled
        // array(
            // e.g. 'DOMDocument' => 'The plugin requires the DOMXML extension.',
        // ),
        'constants'         => '', // disabled
        // array(
            // e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
            // e.g. 'APSPATH' => 'The script cannot be loaded directly.',
        // ),
        'files'             => '', // disabled
        // array(
            // e.g. 'home/my_user_name/my_dir/scripts/my_scripts.php' => 'The required script could not be found.',
        // ),
    );

    /**
     * Used admin pages.
     */
    static public $aAdminPages = array(
        // key => 'page slug'
        'about'     => 'apfl_about',        // the welcome page
        'addon'     => 'apfl_addons',
        'tool'      => 'apfl_tools',
        'help'      => 'apfl_contact',
    );

    /**
     * Used post types.
     */
    static public $aPostTypes = array(
        'demo'      => 'apf_posts',
    );

    /**
     * Used taxonomies.
     */
    static public $aTaxonomies = array(
    );

    /**
     * Sets up static properties.
     * @return      void
     */
    static public function setUp( $sPluginFilePath ) {
        self::$sFilePath = $sPluginFilePath;
        self::$sDirPath  = dirname( self::$sFilePath );
    }

    /**
     * Returns the URL with the given relative path to the plugin path.
     *
     * <h3>Example</h3>
     * <code>
     * AdminPageFrameworkLoader_Registry::getPluginURL( 'asset/css/meta_box.css' );
     * </code>
     * @since       3.5.0
     * @return      string
     */
    public static function getPluginURL( $sRelativePath='' ) {
        if ( isset( self::$_sPluginURLCache ) ) {
            return self::$_sPluginURLCache . $sRelativePath;
        }
        self::$_sPluginURLCache = trailingslashit( plugins_url( '', self::$sFilePath ) );
        return self::$_sPluginURLCache . $sRelativePath;
    }
        /**
         * @since       3.7.9
         */
        static private $_sPluginURLCache;

    /**
     * Returns the information of this class.
     *
     * @since       3.5.0
     * @return      array
     */
    static public function getInfo() {
        $_oReflection = new ReflectionClass( __CLASS__ );
        return $_oReflection->getConstants()
            + $_oReflection->getStaticProperties()
        ;
    }

    /**
     * Stores admin notices.
     * @since       3.5.0
     */
    static public $_aAdminNotices = array();
    /**
     * Sets an admin notice.
     * @since       3.5.0
     * @return      void
     */
    static public function setAdminNotice( $sMessage, $sClassAttribute='error' ) {
        if ( ! is_admin() ) {
            return;
        }
        self::$_aAdminNotices[] = array(
            'message'           => $sMessage,
            'class_attribute'   => trim( $sClassAttribute ) . ' notice is-dismissible',
        );
        add_action( 'admin_notices', array( __CLASS__, '_replyToSetAdminNotice' ) );
    }
        /**
         * Displays the set admin notices.
         * @since       3.5.0
         * @return      void
         */
        static public function _replyToSetAdminNotice() {
            foreach( self::$_aAdminNotices as $_aAdminNotice ) {
                echo "<div class='" . esc_attr( $_aAdminNotice['class_attribute'] ) . " notice is-dismissible'>"
                        ."<p>"
                            . sprintf(
                                '<strong>%1$s</strong>: ' . $_aAdminNotice['message'],
                                self::NAME . ' ' . self::VERSION
                            )
                        . "</p>"
                    . "</div>";
            }
        }

}
// Registry set-up.
AdminPageFrameworkLoader_Registry::setUp( __FILE__ );

// Initial checks. - Do no load if accessed directly, not exiting because the 'uninstall.php' and inclusion list generator will load this file.
if ( ! defined( 'ABSPATH' ) ) {
    return;
}
if ( defined( 'DOING_UNINSTALL' ) && DOING_UNINSTALL ) {
    return;
}

// Set warnings.
function AdminPageFrameworkLoader_Warning() {

    $_bFrameworkLoaded = class_exists( 'AdminPageFramework_Registry', false );
    if (
        ! $_bFrameworkLoaded
        || ! defined( 'AdminPageFramework_Registry::VERSION' ) // backward compatibility
        || version_compare( AdminPageFramework_Registry::VERSION, AdminPageFrameworkLoader_Registry::VERSION, '<' )
    ) {
        AdminPageFrameworkLoader_Registry::setAdminNotice(
            sprintf(
                'The framework has been already loaded and its version is lower than yours. Your framework will not be loaded to avoid unexpected results. Loaded Version - %1$s. Your Version - %2$s.',
                $_bFrameworkLoaded && defined( 'AdminPageFramework_Registry::VERSION' )
                    ? AdminPageFramework_Registry::VERSION
                    : 'unknown',
                AdminPageFrameworkLoader_Registry::VERSION
            )
        );
    }

}
add_action( 'admin_init', 'AdminPageFrameworkLoader_Warning' );

// Include the library file - the development version will be available if you cloned the GitHub repository.
$_sDevelopmentVersionPath = AdminPageFrameworkLoader_Registry::$sDirPath . '/development/admin-page-framework.php';
$_bDebugMode              = defined( 'WP_DEBUG' ) && WP_DEBUG;
$_bLoadDevelopmentVersion = $_bDebugMode && file_exists( $_sDevelopmentVersionPath );
include(
    $_bLoadDevelopmentVersion
        ? $_sDevelopmentVersionPath
        : AdminPageFrameworkLoader_Registry::$sDirPath . '/library/apf/admin-page-framework.php'
);

// Include the framework loader plugin components.
include( AdminPageFramework_Registry::$aClassFiles[ 'AdminPageFramework_PluginBootstrap' ] );
include( AdminPageFrameworkLoader_Registry::$sDirPath . '/include/class/AdminPageFrameworkLoader_Bootstrap.php' );
new AdminPageFrameworkLoader_Bootstrap(
    AdminPageFrameworkLoader_Registry::$sFilePath,
    AdminPageFrameworkLoader_Registry::HOOK_SLUG    // hook prefix
);

/*
 * If you find this framework useful, include it in your project!
 * And please leave a nice comment in the review page, http://wordpress.org/support/view/plugin-reviews/admin-page-framework
 *
 * If you have a suggestion, the GitHub repository is open to anybody so post an issue there.
 * https://github.com/michaeluno/admin-page-framework/issues
 *
 * Happy coding!
 */
