<?php
/* Include dependencies */
include( dirname( __DIR__ ) . '/vendor/autoload.php' );

/* Set necessary paths */
$sTargetBaseDir		= dirname(   // admin-page-framework (project root)
    dirname(                     // tool
        __DIR__             // compile (this directory)
    )
);
$sSourceDirPath              = $sTargetBaseDir . '/development';
$sDestinationDirPath         = $sTargetBaseDir . '/library/apf';
$sHeaderClassName            = 'AdminPageFramework_BeautifiedVersionHeader';
$sHeaderClassPath            = $sSourceDirPath . '/cli/AdminPageFramework_BeautifiedVersionHeader.php';

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) {
    exit( 'Please run the script with a console program.' );
}

echo 'Started...' . PHP_EOL;
$oCompiler = new \AdminPageFrameworkCompiler\Compiler(
    $sSourceDirPath,
    $sDestinationDirPath,
    [
        'output_buffer'        => true,
        'comment_header'       => [
            'class' => $sHeaderClassName,
            'path'  => $sHeaderClassPath,
            'type'  => 'CONSTANTS',
        ],
        'exclude_classes'      => [],
        'css_heredoc_keys'     => [ 'CSSRULES' ],       // to disable inline CSS minification, set an empty array
        'js_heredoc_keys'      => [ 'JAVASCRIPTS' ],    // to disable inline JavaScript minification, set an empty array
        'search'               => [
            'allowed_extensions'    => [ 'php' ],    // e.g. array( 'php', 'inc' )
            // 'exclude_dir_paths'  => array( $sTargetBaseDir . '/include/class/admin' ),
            'exclude_dir_names'     => [ '_document', 'document', 'cli' ],
            'exclude_dir_names_regex' => [
                '/\.bundle$/'
            ],
            'exclude_file_names'    => [
                'AdminPageFramework_InclusionClassFilesHeader.php',
                'AdminPageFramework_MinifiedVersionHeader.php',
                'AdminPageFramework_BeautifiedVersionHeader.php',
            ],
            'is_recursive'            => true,
        ],
        'include'               => [
            'allowed_extensions'    => [
                'js', 'css', 'map', //  resource files
                'txt',       // the license file
            ],
        ],
        'combine'              => [
            'inheritance'     => false,
            'exclude_classes' => [
                'AdminPageFramework_Form_Meta',
                'AdminPageFramework_MetaBox_Page',
            ],
        ],
    ]
);
$oCompiler->run();

echo 'Generating a class map...' . $sCarriageReturn;
$sTargetScanDir  = $sTargetBaseDir . '/library/apf';
$sResultFilePath = $sTargetBaseDir . '/library/apf/admin-page-framework-class-map.php';
$_oClassMap      = new \PHPClassMapGenerator\PHPClassMapGenerator(
    $sTargetScanDir,
    [ $sTargetScanDir ],
    $sResultFilePath,
    array(
        'output_buffer'     => true,
        'output_var_name'	=> 'return',
        'base_dir_var'      => 'AdminPageFramework_Registry::$sDirPath',
        'exclude_classes'   => [
            'AdminPageFramework_MinifiedVersionHeader',
            'AdminPageFramework_ClassMapHeader',            // <-- this is required to distinguish between compiled vs dev versions.
            'AdminPageFramework_BeautifiedVersionHeader',
            'admin-page-framework',
        ],
        'search'            => [
            'exclude_dir_names'      => [
                '_document', 'document', 'cli', 'del.bak', '_del', '_bak', 'apf', '_notes', 'vendor', 'node_modules'
            ],
            'is_recursive'           => true,
            'allowed_extensions'     => [ 'php' ],
            // 'exclude_file_names'     => [ '.min.', ],
            // 'exclude_dir_paths'      => [],
            // 'ignore_note_file_names' => [ 'ignore-class-map.txt' ],
        ],
        'comment_header'    => [
            'class' => 'AdminPageFramework_ClassMapHeader',
            'path'  => $sSourceDirPath . '/cli/AdminPageFramework_ClassMapHeader.php',
            'type'  => 'CONSTANTS',
        ],
    )
);
echo 'Done!' . $sCarriageReturn;