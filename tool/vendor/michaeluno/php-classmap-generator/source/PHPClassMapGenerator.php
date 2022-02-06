<?php
/**
 * Helps to generate class maps.
 * 
 * @author       Michael Uno <michael@michaeluno.jp>
 * @copyright    2020 (c) Michael Uno
 * @license      MIT <http://opensource.org/licenses/MIT>
 */

namespace PHPClassMapGenerator;

use PHPClassMapGenerator\Header\HeaderGenerator;

/**
 * Creates a PHP file that defines an array holding file path with the class key.
 *
 * This is meant to be used for the callback function for the spl_autoload_register() function.
 *
 * @remark		The parsed class file must have a name of the class defined in the file.
 * @version		1.1.1
 */
class PHPClassMapGenerator implements interfacePHPClassMapGenerator {

    use Utility\traitCodeParser;
    use Utility\traitPath;

    public $sBaseDirPath  = '';

    public $aScanDirPaths = array();

    public $sOutputFilePath = '';

    public $sHeaderComment = '';

    public $aItems = array();

    /**
     * @var array
     * @since   1.1.0
     */
    public $aOptions = array();

    /**
     * @var string
     * @since 1.1.0
     */
    public $sCarriageReturn = PHP_EOL;

    /**
     *
     *  - 'header_class_name'	: string	the class name that provides the information for the heading comment of the result output of the minified script.
     *  - 'header_class_path'	: string	(optional) the path to the header class file.
     *  - 'output_buffer'		: boolean	whether or not output buffer should be printed.
     *  - 'exclude_classes' 	: array		an array holding class names to exclude.
     *  - 'base_dir_var'		: string	the variable or constant name that is prefixed before the inclusion path.
     *  - 'search'				: array		the arguments for the directory search options.
     *  - 'header_type'			: string	whether or not to use the docBlock of the header class; otherwise, it will parse the constants of the class. The accepted values are 'CONSTANTS' or 'DOCBLOCK'.
     * ### Example
     * ```
     * array(
     *		'header_class_name'	=>	'HeaderClassForMinifiedVerions',
     *		'output_buffer'	=>	false,
     *		'header_type'	=>	'CONSTANTS',
     *
     * )
     * ```
     *
     * When `CONSTANTS` is passed to the 'header_type' argument, the constants of the header class must include 'Version', 'Name', 'Description', 'URI', 'Author', 'CopyRight', 'License'.
     * ### Example
     * ```
     * class Registry {
     *     const VERSION        = '1.0.0b08';
     *     const NAME           = 'Task Scheduler';
     *     const DESCRIPTION    = 'Provides an enhanced task management system for WordPress.';
     *     const URI            = 'http://en.michaeluno.jp/';
     *     const AUTHOR         = 'miunosoft (Michael Uno)';
     *     const AUTHOR_URI     = 'http://en.michaeluno.jp/';
     *     const COPYRIGHT      = 'Copyright (c) 2014, <Michael Uno>';
     *     const LICENSE        = 'GPL v2 or later';
     *     const CONTRIBUTORS   = '';
     * }
     * ```
     * @param		string			$sBaseDirPath			The base directory path that the inclusion path is relative to.
     * @param		string|array	$asScanDirPaths			The target directory path(s).
     * @param		string			$sOutputFilePath		The destination file path.
     * @param		array			$aOptions				The options array. It takes the following arguments.
     */
    public function __construct( $sBaseDirPath, $asScanDirPaths, $sOutputFilePath, array $aOptions=array() ) {

        if ( ! function_exists( 'token_get_all' ) ) {
            echo 'The function token_get_all() is required.' . self::$_aStructure_Options[ 'carriage_return' ];
            exit;
        }

        $this->_setProperties( $sBaseDirPath, $asScanDirPaths, $sOutputFilePath, $aOptions );

        if ( ! $this->aOptions[ 'do_in_constructor' ] ) {
            return;
        }

        $this->write();

    }

    static protected $_aStructure_Options = array(

        'header_class_name'		=> '',
        'header_class_path'		=> '',
        'header_type'			=> 'DOCBLOCK', // or 'CONSTANT

        'output_buffer'			=> true,
        'exclude_classes'		=> array(
            // 'Foo/Bar' // for name spaced classes, include the name space
        ),

        'base_dir_var'			=> 'CLASS_MAP_BASE_DIR_VAR',
        'output_var_name'		=> '$aClassMap',
        'structure'             => 'CLASS',     // 1.1.0 Accepted values: `CLASS`, `PATH` For `CLASS`, the generated array keys consist of class names. For `PATH` array keys will consist of file paths.
        'do_in_constructor'     => true,        // 1.1.0 Whether to perform the task in the constructor

        // Search options
        'search'	=>	array(
            'allowed_extensions'	=>	array( 'php' ),	 // e.g. array( 'php', 'inc' )
            'exclude_substrings'	=>	array(),	     // e.g. array( '.min.js', '-dont-' )
            'exclude_dir_paths'		=>	array(),
            'exclude_dir_names'		=>	array(),
            'exclude_file_names'     => array(),         // 1.0.3+ includes an file extension.
            'is_recursive'			=>	true,
            'ignore_note_file_names' => array( 'ignore-class-map.txt' ) // 1.1.0 When this option is present and the parsing directory contains a file matching one of the set names, the directory will be skipped.
        ),

    );

    /**
     *
     * @since   1.1.0
     */
    public function write() {
        $this->___write( $this->sOutputFilePath );
    }
        private function ___write( $sOutputFilePath ) {
            if ( file_exists( $sOutputFilePath ) ) {
                unlink( $sOutputFilePath );
            }
            file_put_contents( $sOutputFilePath, $this->getMap(),LOCK_EX );
        }

    /**
     * @since  1.1.0
     * @return string
     */
    public function getMap() {

        $_aData = array(
            mb_convert_encoding( '<?php ' . PHP_EOL . $this->sHeaderComment, 'UTF-8', 'auto' ),
            'return' === $this->aOptions[ 'output_var_name' ]
                ? 'return array(' . PHP_EOL
                : $this->aOptions[ 'output_var_name' ] . ' = array( ' . PHP_EOL,
        );
        foreach( $this->get() as $_sClassName => $_sPath ) {
            $_sClassName = str_replace( '\\', '\\\\', $_sClassName ); // escape backslashes as \t (tab character) will cause a problem
            $_aData[]    = "    " . '"' . $_sClassName . '"' . ' => '
                . $_sPath . ', ' . PHP_EOL;
        }
        $_aData[] = ');';
        return trim( implode( '', $_aData ) );

    }

    /**
     * @return array
     * @since  1.1.0
     */
    public function get() {
        if ( 'CLASS' !== $this->aOptions[ 'structure' ]  ) {
            return $this->aItems;
        }
        return array_map( array( $this, '_getItemConvertedToPath' ), $this->aItems );
    }
        protected function _getItemConvertedToPath( $aItem ) {
            $_sBaseDirVar = $this->aOptions[ 'base_dir_var' ];
            $_sPath		  = str_replace( '\\', '/', $aItem[ 'path' ] );
            $_sPath		  = $this->_getRelativePath( $this->sBaseDirPath, $_sPath );
            return $_sBaseDirVar . ' . "' . $_sPath . '"';
        }

    /**
     * @return array
     */
    public function getItems() {
        return $this->_getItems( $this->aScanDirPaths, $this->sOutputFilePath );
    }
        /**
         * @param  array  $aScanDirPaths
         * @param  string $sOutputFilePath
         * @return array
         */
        protected function _getItems( array $aScanDirPaths, $sOutputFilePath ) {

            $_oFileList     = new FileSystem\FileListGenerator( $aScanDirPaths, $this->aOptions[ 'search' ] );
            $_aFilePaths    = $_oFileList->get();

            // Exclude the output file.
            $_biIndex       = array_search( $sOutputFilePath, $_aFilePaths, true );
            if ( false !== $_biIndex ) {
                unset( $_aFilePaths[ $_biIndex ] );
            }

            if ( 'PATH' === $this->aOptions[ 'structure' ] ) {
                $this->output( sprintf( 'Found %1$s file(s)', count( $_aFilePaths ) ) );
                return $_aFilePaths;
            }

            $_aClasses		= $this->___getFileArrayFormatted( $_aFilePaths );
            $this->output( sprintf( 'Found %1$s file(s) and %2$s item(s)', count( $_aFilePaths ), count( $_aClasses ) ) );
            return $_aClasses;
        }
            /**
             * Sets up the array consisting of class paths with the key of file name w/o extension.
             * @param array $aFilePaths
             * @return array
             */
            private function ___getFileArrayFormatted( array $aFilePaths ) {

                /**
                 * Now the structure of $_aFilePaths looks like:
                 *  array
                 *     0 => string '.../class/MyClass.php'
                 *     1 => string '.../class/MyClass2.php'
                 *     2 => string '.../class/MyClass3.php'
                 *     ...
                 *
                 */
                $_aFiles = array();
                foreach( $aFilePaths as $_sFilePath ) {

                    $_sPHPCode      = $this->_getPHPCode( $_sFilePath );
                    $_aFileInfo     = array(    // the file name without extension will be assigned to the key
                        'path'              => $_sFilePath,
                        'code'              => $_sPHPCode ? trim( $_sPHPCode ) : '',
                        'dependency'        => $this->_getParentClass( $_sPHPCode ),
                    ) + $this->_getDefinedObjectConstructs( '<?php ' . $_sPHPCode );

                    // the file name without extension will be assigned to the key
                    foreach( array_merge( $_aFileInfo[ 'classes' ], $_aFileInfo[ 'interfaces' ], $_aFileInfo[ 'traits' ] ) as $_sClassName ) {
                        $_aFiles[ $_sClassName ] = $_aFileInfo;
                    }

                }
                return $_aFiles;

            }



    /**
     * @param  string       $sBaseDirPath
     * @param  array|string $asScanDirPaths
     * @param  string       $sOutputFilePath
     * @param  array        $aOptions
     * @since  1.1.0
     */
    protected function _setProperties( $sBaseDirPath, $asScanDirPaths, $sOutputFilePath, array $aOptions ) {
        $this->sBaseDirPath     = $this->_getPathFormatted( $sBaseDirPath );
        $this->sOutputFilePath  = $this->_getPathFormatted( $sOutputFilePath );
        $this->aOptions         = $this->___getOptionsFormatted( $aOptions );
        $this->sCarriageReturn	= php_sapi_name() == 'cli' ? PHP_EOL : '<br />';
        $this->aScanDirPaths    = ( array ) $asScanDirPaths;
        $this->aScanDirPaths    = array_map( array( $this, '_getPathFormatted' ), $this->aScanDirPaths );
        $this->___setItems();
    }
        /**
         * @param  array $aOptions
         * @return array
         * @since  1.1.0
         */
        private function ___getOptionsFormatted( array $aOptions ) {
            $aOptions			    = $aOptions + self::$_aStructure_Options;
            $aOptions[ 'search' ]	= $aOptions[ 'search' ] + self::$_aStructure_Options[ 'search' ];
            return $aOptions;
        }
        /**
         * @since   1.1.0
         */
        private function ___setItems() {

            $this->output( 'Searching files under the directories: ' . implode( ', ', $this->aScanDirPaths ) );

            $this->aItems = $this->getItems();

            $this->___setProjectHeaderComment();

            $this->___sort( $this->aItems );

        }
            private function ___setProjectHeaderComment() {
                try {
                    $_oHeaderGenerator    = new HeaderGenerator( $this->aItems, $this->aOptions );
                    $this->sHeaderComment = $_oHeaderGenerator->get();
                    if ( ! $this->sHeaderComment ) {
                        throw new \ReflectionException( 'No header comment generated.' );
                    }
                } catch ( \ReflectionException $e ) {
                    $this->output( 'Could not set a project header comment. ' . $e->getMessage() );
                }
                $this->output( $this->sHeaderComment );
            }

            /**
             * Sort the classes - in some PHP versions, parent classes must be defined before extended classes.
             * @since   1.1.0
             * @param array &$aItems
             */
            private function ___sort( array &$aItems ) {
                $aItems = $this->___getSortedItems( $aItems, $this->aOptions[ 'exclude_classes' ] );
                $this->output( sprintf( 'Sorted %1$s item(s)', count( $aItems ) ) );
            }
                private function ___getSortedItems( array $aItems, array $aExcludingClassNames ) {

                    if ( 'CLASS' !== $this->aOptions[ 'structure' ] ) {
                        return $aItems;
                    }

                    $aItems = $this->___getDefinedObjectConstructsExtracted( $aItems, $aExcludingClassNames );
                    foreach( $aItems as $_sClassName => $_aFile ) {
                        if ( in_array( $_sClassName, $aExcludingClassNames ) ) {
                            unset( $aItems[ $_sClassName ] );
                        }
                    }
                    return $aItems;

                }
                    private function ___getDefinedObjectConstructsExtracted( array $aItems, array $aExcludingClassNames ) {

                        $_aAdditionalClasses = array();
                        foreach( $aItems as $_sClassName => $_aItem ) {
                            $_aObjectConstructs = array_merge( $_aItem[ 'classes' ], $_aItem[ 'traits' ], $_aItem[ 'interfaces' ] );
                            foreach( $_aObjectConstructs as $_sAdditionalClass ) {
                                if ( in_array( $_sAdditionalClass, $aExcludingClassNames ) ) {
                                    continue;
                                }
                                $_aAdditionalClasses[ $_sAdditionalClass ] = $_aItem;
                            }
                        }
                        return $_aAdditionalClasses;

                    }


    /**
     * Echoes the passed string.
     *
     * @param       string      $sText
     * @since       1.0.0
     * @return      void
     */
    public function output( $sText ) {
        if ( ! $this->aOptions[ 'output_buffer' ] ) {
            return;
        }
        echo $sText . $this->sCarriageReturn;
    }

}