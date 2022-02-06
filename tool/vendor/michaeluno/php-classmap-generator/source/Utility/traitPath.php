<?php


namespace PHPClassMapGenerator\Utility;


trait traitPath {

    /**
     * Formats the paths.
     *
     * This is necessary to check excluding paths because the user may pass paths with a forward slash but the system may use backslashes.
     * @param string|array  $asDirPaths
     * @return array
     */
    protected function _getPathsFormatted( $asDirPaths ) {

        $_aFormattedDirPaths = array();
        $_aDirPaths = is_array( $asDirPaths ) ? $asDirPaths : array( $asDirPaths );
        foreach( $_aDirPaths as $_sPath ) {
            $_aFormattedDirPaths[] = $this->_getPathFormatted( $_sPath );
        }
        return $_aFormattedDirPaths;

    }

    /**
     * @param       string  $sPath
     * @return      string
     */
    protected function _getPathFormatted( $sPath ) {
        return rtrim( str_replace( '\\', '/', $sPath ), '/' );
    }

    /**
     * Calculates the relative path from the given path.
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function _getRelativePath( $from, $to ) {

        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from     = explode('/', $from);
        $to       = explode('/', $to);
        $relPath  = $to;

        foreach($from as $depth => $dir) {
            // find first non-matching dir
            if($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }

        $relPath = implode( '/', $relPath );
        return ltrim( $relPath, '.' );
    }


}