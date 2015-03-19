<?php
$_sTestsDirPath                 = getenv( 'WP_TESTS_DIR' );
// $_sSystemTempDirPath         = getenv( 'TEMP' ) ? getenv( 'TEMP' ) : '/tmp';
$GLOBALS['_sProjectDirPath']    = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
$_sTestSiteDirPath              = dirname( dirname( dirname( $GLOBALS['_sProjectDirPath'] ) ) );
if ( ! $_sTestsDirPath ) {
    $_sTestsDirPath = $_sTestSiteDirPath . '/wordpress-tests-lib';
}        

// Referenced from bootstrap.php
$GLOBALS['_sTestsDirPath'] = $_sTestsDirPath;

require_once $GLOBALS['_sTestsDirPath'] . '/includes/functions.php';


function _loadPluginManually() {
	require $GLOBALS['_sProjectDirPath'] . '/admin-page-framework-loader.php';
}
tests_add_filter( 'muplugins_loaded', '_loadPluginManually' );

// Store the value of the $file variable as it will be changed by WordPress.
$_file = $file;
require $GLOBALS['_sTestsDirPath'] . '/includes/bootstrap.php';
$file = $_file;

activate_plugin( 'admin-page-framework/admin-page-framework-loader.php' );