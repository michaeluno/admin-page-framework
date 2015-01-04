<?php
/**
 * Minifies PHP files into a single file.
 *
 */

/* Set necessary paths */
$sTargetBaseDir		= dirname( dirname( dirname( __FILE__ ) ) );
$sTargetDir			= $sTargetBaseDir . '/development';
$sResultFilePath	= $sTargetBaseDir . '/library/admin-page-framework.min.php';
$sLicenseFileName	= 'MIT-LICENSE.txt';
$sLicenseFilePath	= $sTargetDir . '/' . $sLicenseFileName;
$sHeaderClassName	= 'AdminPageFramework_MinifiedVersionHeader';
$sHeaderClassPath	= $sTargetDir . '/_model/AdminPageFramework_MinifiedVersionHeader.php';

// For get about the rest.

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { exit; }

/* Include necessary files */
require( dirname( __FILE__ ) . '/class/PHP_Class_Files_Minifier.php' );

/* Check the permission to write. */
if ( ! file_exists( $sResultFilePath ) ) {
	file_put_contents( $sResultFilePath, '', FILE_APPEND | LOCK_EX );
}
if ( 
	( file_exists( $sResultFilePath ) && ! is_writable( $sResultFilePath ) )
	|| ! is_writable( dirname( $sResultFilePath ) ) 	
) {
	exit( sprintf( 'The permission denied. Make sure if the folder, %1$s, allows to modify/create a file.', dirname( $sResultFilePath ) ) );
}

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;
new PHP_Class_Files_Minifier( 
	$sTargetDir, 
	$sResultFilePath, 
	array(
		'header_class_name'	=>	$sHeaderClassName,
		'header_class_path'	=>	$sHeaderClassPath,
		'output_buffer'		=>	true,
		'header_type'		=>	'CONSTANTS',	
		'exclude_classes'	=>	array(
			'AdminPageFramework_MinifiedVersionHeader', 
			'AdminPageFramework_InclusionClassFilesHeader',
			'admin-page-framework-include-class-list',
		),
		'search'			=>	array(
			'allowed_extensions'	=>	array( 'php' ),	// e.g. array( 'php', 'inc' )
			// 'exclude_dir_paths'		=>	array( $sTargetBaseDir . '/include/class/admin' ),
			'exclude_dir_names'		=>	array( '_document' ),
			'is_recursive'			=>	true,
		),			        
	)
);

// Copy the license text.
copy( $sLicenseFilePath, dirname( $sResultFilePath ) . '/' . $sLicenseFileName );

echo 'Done!' . $sCarriageReturn;