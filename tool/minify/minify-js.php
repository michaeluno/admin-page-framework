<?php
/* Set necessary paths */
$sTargetBaseDir		= dirname( dirname( dirname( __FILE__ ) ) );
$aTargetDirs        = array(
    $sTargetBaseDir . '/example/library/path2-custom-field-type',
);

/* If accessed from a browser, exit. */
$bIsCLI				= php_sapi_name() == 'cli';
$sCarriageReturn	= $bIsCLI ? PHP_EOL : '<br />';
if ( ! $bIsCLI ) { 
    exit( 'Please run the script with a console program.' );
}

/* Include necessary files */
require( dirname( __DIR__ ) . '/vendor/autoload.php' );
require( dirname( __FILE__ ) . '/class/vendor/autoload.php' );

/* Create a minified version of the framework. */
echo 'Started...' . $sCarriageReturn;
$_oGenerator = new \PHPClassMapGenerator\PHPClassMapGenerator(
    $sTargetBaseDir,
    $aTargetDirs,
    '',
    array(
        'do_in_constructor'  => false,
        'output_buffer'      => true,
        'structure'          => 'PATH',
        'search'            => [
            'allowed_extensions'     => [ 'js' ],
            'exclude_dir_paths'      => [],
            'exclude_dir_names'      => [ '_del', '_bak', 'apf', 'library', 'src' ],
            'exclude_file_names'     => [ '.min.', 'postcss.config', 'webpack.config' ],
            'is_recursive'           => true,
            'ignore_note_file_names' => [ 'ignore-js-min.txt' ],
        ],
    )
);


$_aFileInfoStruct = [
    'name'     => '@name',
    'version'  => '@version',
];
$_iCount = 1;
foreach( $_oGenerator->get() as $_sScriptPath ) {

    $_sDirPath       = dirname( $_sScriptPath );
    $_sBaseNameWOExt = pathinfo( $_sScriptPath, PATHINFO_FILENAME );
    $_sMinScriptPath = $_sDirPath . '/' . $_sBaseNameWOExt . '.min.js';
    $_sPrevContent   = file_exists( $_sMinScriptPath ) ? trim( file_get_contents( $_sMinScriptPath ) ) : '';

    $_aFileInfo      = $_oGenerator->getFileHeaderComment( $_sScriptPath, $_aFileInfoStruct );
    $_sScriptName    = trim( "{$_aFileInfo[ 'name' ]} {$_aFileInfo[ 'version' ]}" );
    $_sHeader        = $_sScriptName ? "/* {$_sScriptName} */" . PHP_EOL : '';
    $_oMinifier      = Asika\Minifier\MinifierFactory::create('js' );
    $_oMinifier->addFile( $_sScriptPath );
    $_sContent       = trim( $_sHeader . $_oMinifier->minify() );
    if ( $_sPrevContent === $_sContent ) {
        continue;
    }

    file_put_contents( $_sMinScriptPath, $_sContent );
    echo "{$_iCount}: Writing to " . $_sMinScriptPath . $sCarriageReturn;
    $_iCount++;
}
echo 'Done!' . $sCarriageReturn;