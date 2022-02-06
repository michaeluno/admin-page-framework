<?php


namespace PHPClassMapGenerator\FileSystem;


trait traitFileSearch {

    /**
     * The recursive version of the glob() function.
     * @param  string  $sPathPatten
     * @param  integer $nFlags
     * @param  array   $aExcludeDirPaths
     * @param  array   $aExcludeDirNames
     * @param  array   $aExcludeFileNames
     * @param  array   $aIgnoreNotes
     * @param  array   $aExcludedSubstrings
     * @return array
     */
    protected function _doRecursiveGlob( $sPathPatten, $nFlags=0, array $aExcludeDirPaths=array(), array $aExcludeDirNames=array(), array $aExcludeFileNames=array(), array $aIgnoreNotes=array(), array $aExcludedSubstrings=array() ) {

        if ( $this->___fileExists( $aIgnoreNotes, dirname( $sPathPatten ) . '/' ) ) {
            return array();
        }

        $_aFiles    = $this->___getFilesByGlob( $sPathPatten, $nFlags, $aExcludeFileNames, $aExcludedSubstrings );
        $_aDirs     = glob(dirname( $sPathPatten ) . DIRECTORY_SEPARATOR . '*',  GLOB_ONLYDIR|GLOB_NOSORT );
        $_aDirs     = is_array( $_aDirs ) ? $_aDirs : array();
        foreach ( $_aDirs as $_sDirPath ) {
            $_sDirPath        = $this->_getPathFormatted( $_sDirPath );
            if ( in_array( $_sDirPath, $aExcludeDirPaths ) ) {
                continue;
            }
            if ( in_array( pathinfo( $_sDirPath, PATHINFO_BASENAME ), $aExcludeDirNames ) ) {
                continue;
            }
            $_aFiles    = array_merge(
                $_aFiles,
                $this->_doRecursiveGlob(
                    $_sDirPath . DIRECTORY_SEPARATOR . basename( $sPathPatten ),
                    $nFlags,
                    $aExcludeDirPaths,
                    $aExcludeDirNames,
                    $aExcludeFileNames,
                    $aIgnoreNotes,
                    $aExcludedSubstrings
                )
            );

        }
        return $_aFiles;

    }
        /**
         * Checks whether a file exists.
         *
         * @remark Checks all the paths given as array members and at least one of them exists, the method returns true.
         * @param  array   $aFilePaths
         * @param  string  $sSuffix     The path suffix to prepend to the path set in the array.
         * @return boolean
         */
        private function ___fileExists( array $aFilePaths, $sSuffix='' ) {
            foreach( $aFilePaths as $_sFilePath ) {
                if ( file_exists( $sSuffix . $_sFilePath ) ) {
                    return true;
                }
            }
            return false;
        }

        /**
         * @param  string  $sPathPatten
         * @param  integer $nFlags
         * @param  array   $aExcludeFileNames
         * @param  array   $aExcludedSubstrings
         * @return array
         */
        private function ___getFilesByGlob( $sPathPatten, $nFlags, array $aExcludeFileNames, array $aExcludedSubstrings ) {

            $_aFiles    = glob( $sPathPatten, $nFlags );
            $_aFiles    = is_array( $_aFiles ) ? $_aFiles : array();    // glob() can return false.
            $_aFiles    = array_filter( $_aFiles, 'is_file' );   // drop directories that happen to be included. This can occur with the pattern like *.js and a directory named something.js.
            $_aFiles    = array_map( array( $this, '_getPathFormatted' ), $_aFiles );
            $_aFiles    = $this->___dropExcludingFiles( $_aFiles, $aExcludeFileNames, $aExcludedSubstrings );
            return $_aFiles;

        }
            /**
             * Removes files from the generated list that is set in the 'exclude_file_names' argument of the searh option array.
             * @since       1.0.6
             * @param       array   $aFiles
             * @param       array   $aExcludingFileNames
             * @param       array   $aExcludedSubstrings
             * @return      array
             */
            private function ___dropExcludingFiles( array $aFiles, array $aExcludingFileNames=array(), array $aExcludedSubstrings=array() ) {

                if ( empty( $aExcludingFileNames ) && empty( $aExcludedSubstrings ) ) {
                    return $aFiles;
                }
                foreach( $aFiles as $_iIndex => $_sPath ) {
                    $_sBaseFileName = basename( $_sPath );
                    if ( $this->___hasSubstring( $_sBaseFileName, $aExcludingFileNames ) ) {
                        unset( $aFiles[ $_iIndex ] );
                        continue;
                    }
                    if ( $this->___hasSubstring( $_sPath, $aExcludedSubstrings ) ) {
                        unset( $aFiles[ $_iIndex ] );
                        continue;
                    }
                }
                return $aFiles;

            }
                /**
                 *
                 * @param  string  $sString
                 * @param  array   $aNeedles
                 * @return boolean `true` if at lease one match is found. `false` if none of the needles match.
                 */
                private function ___hasSubstring( $sString, array $aNeedles ) {
                    foreach( $aNeedles as $_sNeedle ) {
                        if ( false !== strpos( $sString, $_sNeedle ) ) {
                            return true;
                        }
                    }
                    return false;
                }



    /**
     * Constructs the file pattern of the file extension part used for the glob() function with the given file extensions.
     * @param  array $aExtensions
     * @return string
     */
    protected function _getGlobPatternExtensionPart( array $aExtensions=array( 'php', 'inc' ) ) {
        return empty( $aExtensions )
            ? '*'
            : '{' . implode( ',', $aExtensions ) . '}';
    }


}