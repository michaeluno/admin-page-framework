<?php


namespace PHPClassMapGenerator\FileSystem;

use PHPClassMapGenerator\Utility\traitPath;

class FileListGenerator {

    use traitFileSearch;
    use traitPath;

    public $aDirPaths = '';
    public $aSearchOptions = [];
    
    /**
     * FileListGenerator constructor.
     * @param array|string $asDirPaths
     * @param array $aSearchOptions
     */
    public function __construct( $asDirPaths, array $aSearchOptions ) {
        $this->aDirPaths = is_array( $asDirPaths ) ? $asDirPaths : array( $asDirPaths );
        $this->aSearchOptions = $aSearchOptions;
    }

    /**
     * @return array
     */
    public function get() {
        $_aFiles = array();
        foreach( $this->aDirPaths as $_sDirPath ) {
            $_aFilesPerDir = $this->___getFilePathsPerDirectory( $_sDirPath );
            $_aFiles       = array_merge( $_aFilesPerDir, $_aFiles );
        }
        return array_unique( $_aFiles );
    }
        /**
         * Returns an array of scanned file path.
         *
         * The returning array structure looks like this:
         *   array
         *     0 => string '.../class/MyClass.php'
         *     1 => string '.../class/MyClass2.php'
         *     2 => string '.../class/MyClass3.php'
         *     ...
         * @param string
         * @return array
         */
        private function ___getFilePathsPerDirectory( $sDirPath ) {

            $sDirPath            = rtrim( $sDirPath, '\\/' ) . DIRECTORY_SEPARATOR;    // ensures the trailing (back/)slash exists.
            $_aExcludingDirPaths = $this->_getPathsFormatted( $this->aSearchOptions[ 'exclude_dir_paths' ] );

            if ( defined( 'GLOB_BRACE' ) ) {    // in some OSes this flag constant is not available.
                $_sFileExtensionPattern = $this->_getGlobPatternExtensionPart( $this->aSearchOptions[ 'allowed_extensions' ] );
                $_aFilePaths = $this->aSearchOptions[ 'is_recursive' ]
                    ? $this->_doRecursiveGlob(
                        $sDirPath . '*.' . $_sFileExtensionPattern,
                        GLOB_BRACE,
                        $_aExcludingDirPaths,
                        ( array ) $this->aSearchOptions[ 'exclude_dir_names' ],
                        ( array ) $this->aSearchOptions[ 'exclude_file_names' ],
                        ( array ) $this->aSearchOptions[ 'ignore_note_file_names' ],
                        ( array ) $this->aSearchOptions[ 'exclude_substrings' ]
                    )
                    : ( array ) glob( $sDirPath . '*.' . $_sFileExtensionPattern, GLOB_BRACE );
                return array_filter( $_aFilePaths );    // drop non-value elements.
            }

            // For the Solaris operation system.
            $_aFilePaths = array();
            foreach( $this->aSearchOptions[ 'allowed_extensions' ] as $__sAllowedExtension ) {
                $__aFilePaths = $this->aSearchOptions[ 'is_recursive' ]
                    ? $this->_doRecursiveGlob(
                        $sDirPath . '*.' . $__sAllowedExtension,
                        0,
                        $_aExcludingDirPaths,
                        ( array ) $this->aSearchOptions[ 'exclude_dir_names' ],
                        ( array ) $this->aSearchOptions[ 'exclude_file_names' ],
                        ( array ) $this->aSearchOptions[ 'ignore_note_file_names' ],
                        ( array ) $this->aSearchOptions[ 'exclude_substrings' ]
                    )
                    : ( array ) glob( $sDirPath . '*.' . $__sAllowedExtension );
                $_aFilePaths = array_merge( $__aFilePaths, $_aFilePaths );
            }
            return array_unique( array_filter( $_aFilePaths ) );

        }
    
}