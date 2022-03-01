<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with files which do not use WordPress functions.
 *
 * @since       3.4.6
 * @extends     AdminPageFramework_Utility_URL
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_File extends AdminPageFramework_Utility_URL {

    /**
     * Retrieves the contents of last n lines of a file.
     *
     * This is used to read log files.
     *
     * @since       3.4.6
     * @param       array|string        $asPath         The file path to parse. If array of paths is given, only the first item is used. Allowing multiple paths to be passed is for the ini setting value of PHP error log paths that can be an array.
     * @param       integer             $iLines         The number of lines to read.
     * @return      string
     */
    static public function getFileTailContents( $asPath=array(), $iLines=1 ) {

        $_sPath  = self::_getFirstItem( $asPath );
        if ( ! @is_readable( $_sPath ) ) {
            return '';
        }
        return trim(
            implode(
                '',
                array_slice(
                    file( $_sPath ),
                    - $iLines
                )
            )
        );

    }
        /**
         * Returns a first item of an array.
         * @since       3.5.4
         * @return      string
         */
        static private function _getFirstItem( $asItems ) {
            $_aItems  = is_array( $asItems ) ? $asItems : array( $asItems );
            $_aItems  = array_values( $_aItems );
            return ( string ) array_shift( $_aItems );
        }
    /**
     * Sanitizes the given file name.
     *
     * @since       3.4.6
     * @return      string
     */
    static public function sanitizeFileName( $sFileName, $sReplacement='_' ) {

        // Remove anything which isn't a word, white space, number
        // or any of the following characters -_~,;:[]().
        $sFileName = preg_replace( "([^\w\s\d\-_~,;:\[\]\(\).])", $sReplacement, $sFileName );

        // Remove any runs of periods.
        return preg_replace( "([\.]{2,})", '', $sFileName );

    }

}
