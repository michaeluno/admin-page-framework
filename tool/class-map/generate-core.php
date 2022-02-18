<?php
/**
 * Class Map Generator Script
 * @version 1.0.0
 */
/* Configuration */
$sTargetBaseDir     = dirname( dirname( __DIR__ ) );
$sResultFilePath    = $sTargetBaseDir . '/development/admin-page-framework-class-map.php';

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { exit; }

/* Include necessary files */
require( dirname( __DIR__ ) . '/vendor/autoload.php' );

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

$_oGenerator = new \PHPClassMapGenerator\PHPClassMapGenerator(
    $sTargetBaseDir . '/development',
    [ $sTargetBaseDir . '/development' ],  // scan dirs
    $sResultFilePath,
    [
        'header_class_name'	=>	'AdminPageFramework_ClassMapHeader',
        'header_type'		=>	'CONSTANTS',
        'output_buffer'     => true,
        'output_var_name'	=>	'return',
        'base_dir_var'      => 'AdminPageFramework_Registry::$sDirPath',
        // 'header_class_path'    => $sTargetBaseDir . '/development/document/AdminPageFramework_InclusionClassFilesHeader.php',
        'exclude_classes'		=> [
            // 'AdminPageFramework_ClassMapHeader',  <-- this will be listed but not in the beautified version, used to detect whether it is a development version or not.
            'AdminPageFramework_MinifiedVersionHeader',
            'AdminPageFramework_BeautifiedVersionHeader',
            'admin-page-framework',
            'AdminPageFramework_Registry_Base',
            'AdminPageFramework_Registry',
            'AdminPageFramework_Bootstrap',
        ],
        'search'            => [
            'exclude_dir_names'      => [ '_document', 'document', 'del.bak', '_del', '_bak', 'apf', '_notes', 'vendor', 'node_modules', 'asset', 'assets' ],
            'is_recursive'           => true,
            'allowed_extensions'     => [ 'php' ],
            // 'exclude_file_names'     => [ '.min.', ],
            // 'exclude_dir_paths'      => [],
            // 'ignore_note_file_names' => [ 'ignore-class-map.txt' ],
        ],
    ]
);

echo 'Done!' . $sCarriageReturn;