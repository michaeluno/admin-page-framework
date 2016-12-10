<?php
$_sTestsDirPath                 = getenv( 'WP_TESTS_DIR' );
// $_sSystemTempDirPath         = getenv( 'TEMP' ) ? getenv( 'TEMP' ) : '/tmp';
$GLOBALS[ '_sProjectDirPath' ]  = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
$_sTestSiteDirPath              = dirname( dirname( dirname( $GLOBALS['_sProjectDirPath'] ) ) );
if ( ! $_sTestsDirPath ) {
    $_sTestsDirPath = $_sTestSiteDirPath . '/wordpress-tests-lib';
}

define( 'WP_USE_THEMES', false );

// Referenced from bootstrap.php
$GLOBALS[ '_sTestsDirPath' ] = $_sTestsDirPath;
require_once $GLOBALS[ '_sTestsDirPath' ] . '/includes/functions.php';

/**
 * Called when all must-use plugins are loaded.
 * @deprecated      3.7.9       Seems to work without it.
 */
// function _loadPluginManually() {
    // require_once( $GLOBALS[ '_sProjectDirPath' ] . '/admin-page-framework-loader.php' );
// }
// tests_add_filter( 'muplugins_loaded', '_loadPluginManually' ); 

// Store the value of the $file variable as it will be changed by WordPress.
$_file = $file;
require_once( $GLOBALS[ '_sTestsDirPath' ] . '/includes/bootstrap.php' );
$file = $_file;

// Loading the library bootstrap before activating the loader plugin makes it possible not to load the development version.
// To test the development version just comment out this line.
$_bUseCompiled = true;
require_once( $GLOBALS[ '_sProjectDirPath' ] . '/library/apf/admin-page-framework.php' );

$_noActivated = activate_plugin( 'admin-page-framework/admin-page-framework-loader.php' );
$GLOBALS[ 'apf_loader_activated' ] = null === $_noActivated;

// Console messages
codecept_debug( 'Testing against Complied Files: ' . ( empty( $_bUseCompiled ) ? 'No' : 'Yes' ) );        
codecept_debug( 'Activated Admin Page Framework - Loader: ' . ( null === $_noActivated ? 'Yes' : 'No' ) );

class APF_UnitTestCase extends \WP_UnitTestCase {
    /**
     * @remark      Fixes the error: [PHPUnit_Framework_Exception] mysqli_query(): Couldn't fetch mysqli.
     */
    public static function tearDownAfterClass() {

        PHPUnit_Framework_TestCase::tearDownAfterClass();

        // This causes an error: [PHPUnit_Framework_Exception] mysqli_query(): Couldn't fetch mysqli
        // _delete_all_data();
        // self::flush_cache();

        $c = self::get_called_class();
        if ( ! method_exists( $c, 'wpTearDownAfterClass' ) ) {
            return;
        }

        call_user_func( array( $c, 'wpTearDownAfterClass' ) );
        self::commit_transaction();

    }
}