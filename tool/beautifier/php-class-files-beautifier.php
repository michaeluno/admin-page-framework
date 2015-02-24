<?php
/**
 * Beautify PHP files.
 *
 */

/* Set necessary paths */
$sTargetBaseDir		        = dirname( dirname( dirname( __FILE__ ) ) );
$sTargetDir			        = $sTargetBaseDir . '/development';
$sDestinationDirectoryPath	= $sTargetBaseDir . '/library/admin-page-framework';
$sLicenseFileName	        = 'LICENSE.txt';
$sLicenseFilePath	        = $sDestinationDirectoryPath . '/' . $sLicenseFileName;
$sHeaderClassName	        = 'AdminPageFramework_BeautifiedVersionHeader';
$sHeaderClassPath	        = $sTargetDir . '/factory/AdminPageFramework_Factory/model/AdminPageFramework_BeautifiedVersionHeader.php';

// For get about the rest.

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { exit; }

/* Include necessary files */
require( dirname( __FILE__ ) . '/class/PHP_Class_Files_Beautifier.php' );

/* Check the permission to write. */
if (  ! is_writable( dirname( $sDestinationDirectoryPath ) ) ) {
	exit( sprintf( 'The permission denied. Make sure if the folder, %1$s, allows to modify/create a file.', dirname( $sDestinationDirectoryPath ) ) );
}

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;
new PHP_Class_Files_Beautifier( 
	$sTargetDir, 
	$sDestinationDirectoryPath, 
	array(
		'header_class_name'	=>	$sHeaderClassName,
		'header_class_path'	=>	$sHeaderClassPath,
		'output_buffer'		=>	true,
		'header_type'		=>	'CONSTANTS',	
		'exclude_classes'	=>	array(
			'AdminPageFramework_InclusionClassFilesHeader',
			'AdminPageFramework_MinifiedVersionHeader',
			'admin-page-framework-include-class-list',
		),
		'search'			=>	array(
			'allowed_extensions'	=>	array( 'php' ),	// e.g. array( 'php', 'inc' )
			// 'exclude_dir_paths'		=>	array( $sTargetBaseDir . '/include/class/admin' ),
			'exclude_dir_names'		=>	array( '_document', 'document' ),
			'is_recursive'			=>	true,
		),			    
	)
);

// Copy the license text.
@copy( $sLicenseFilePath, dirname( $sDestinationDirectoryPath ) . '/' . $sLicenseFileName );

echo 'Done!' . $sCarriageReturn;