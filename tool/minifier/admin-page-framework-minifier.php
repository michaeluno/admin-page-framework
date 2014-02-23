<?php
/* If run from a browser, exit. */
if ( php_sapi_name() != 'cli' ) exit;

/* Include necessary files */
require( dirname( __FILE__ ) . '/class/AdminPageFramework_Minifiler_ProgressBuffer.php' );
require( dirname( __FILE__ ) . '/class/AdminPageFramework_Minifier.php' );

/* Set necessary paths */
$sPluginBaseDir = dirname( dirname( dirname( __FILE__ ) ) );
	/* Set the source paths */
	$sSourceFilePath = $sPluginBaseDir . '/development/admin-page-framework.php';
	$sLicenseFileName = 'MIT-LICENSE.txt';
	$sLicenseFilePath = dirname( $sSourceFilePath ) . '/' . $sLicenseFileName;
	
	/* Set the location for the script output */
	$sResultFilePath = $sPluginBaseDir . '/library/admin-page-framework.min.php';
	
	/* Check the file existence. */
	if ( ! file_exists( $sSourceFilePath ) ) die( '<p>The main library file does not exist.</p>' );
	if ( ! file_exists( $sLicenseFilePath ) ) die( '<p>The license file does not exist.</p>' );
	
	/* Check the permission to write. */
	if ( 
		( file_exists( $sResultFilePath ) && ! is_writable( $sResultFilePath ) )
		|| ! is_writable( dirname( $sResultFilePath ) ) 	
	) 
		die( sprintf( '<p>The permission denied. Make sure if the folder, %1$s, allows to modify/create a file.</p>', dirname( $sResultFilePath ) ) );
	
/* Echo progress report. */	
$oProgressBuffer = new AdminPageFramework_Minifiler_ProgressBuffer( 'Admin Page Framework Minifier Script' );	
$oProgressBuffer->showText( 'Starting...' );

/* Create a minified version of the framework. */
$oMinify = new AdminPageFramework_Minifier( $sSourceFilePath, $sResultFilePath );

copy( $sLicenseFilePath, dirname( $sResultFilePath ) . '/' . $sLicenseFileName );

/* Update the progress output. */		
$oProgressBuffer->showText( 'Done!' );

