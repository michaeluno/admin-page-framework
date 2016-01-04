<?php
/**
 * Beautify PHP files.
 *
 */
$_nStart = microtime( true );

/* Set necessary paths */
$sTargetBaseDir              = dirname( dirname( dirname( __FILE__ ) ) );
$sTargetDir                  = $sTargetBaseDir . '/development';
$sDestinationDirectoryPath   = $sTargetBaseDir . '/library/apf';
$sLicenseFileName            = 'LICENSE.txt';
$sLicenseSourceFilePath      = $sTargetDir . '/' . $sLicenseFileName;
$sHeaderClassName            = 'AdminPageFramework_BeautifiedVersionHeader';
$sHeaderClassPath            = $sTargetDir . '/cli/AdminPageFramework_BeautifiedVersionHeader.php';

// For get about the rest.

/* If accessed from a browser, exit. */
$bIsCLI                      = php_sapi_name() == 'cli';
$sCarriageReturn             = $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { 
    exit; 
}

/* Include necessary files */
require( dirname( __FILE__ ) . '/class/PHP_Class_Files_Beautifier.php' );

/* Check the permission to write. */
if (  ! is_writable( dirname( $sDestinationDirectoryPath ) ) ) {
    exit( sprintf( 'The permission denied. Make sure if the folder, %1$s, allows the script to modify/create a file.', dirname( $sDestinationDirectoryPath ) ) );
}

/* Create a beautified version of the framework. */
echo 'Started...' . $sCarriageReturn;
new PHP_Class_Files_Beautifier( 
    $sTargetDir, 
    $sDestinationDirectoryPath, 
    array(
        'header_class_name'    => $sHeaderClassName,
        'header_class_path'    => $sHeaderClassPath,
        'output_buffer'        => true,
        'header_type'          => 'CONSTANTS',    
        'exclude_classes'      => array(
        ),
        'search'               => array(
            'allowed_extensions'    => array( 'php' ),    // e.g. array( 'php', 'inc' )
            // 'exclude_dir_paths'  => array( $sTargetBaseDir . '/include/class/admin' ),
            'exclude_dir_names'     => array( '_document', 'document', 'cli' ),
            'exclude_file_names'    => array(
                'AdminPageFramework_InclusionClassFilesHeader.php',
                'AdminPageFramework_MinifiedVersionHeader.php',
                'AdminPageFramework_BeautifiedVersionHeader.php',            
            ),
            'is_recursive'            => true,
        ),                
        'combine'              => array(
            'exclude_classes' => array( 'AdminPageFramework_Form_Meta' ),
        ),
    )
);

// Copy the license text.
@copy( 
    $sLicenseSourceFilePath,  // source
    $sDestinationDirectoryPath . '/' . $sLicenseFileName     // destination
);

// Generate a inclusion class list.
include( 'create-list-admin-page-framework-beautified-version.php' );

echo 'Done!' . $sCarriageReturn;
echo 'Elapsed Seconds: ' . ( microtime( true ) - $_nStart ) . $sCarriageReturn;