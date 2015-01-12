<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with files which do not use WordPress functions.
 *
 * @since       3.4.6
 * @extends     AdminPageFramework_Utility_URL
 * @package     AdminPageFramework
 * @subpackage  Utility
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
     */
    static public function getFileTailContents( $asPath=array(), $iLines=1 ) {
        
        $_aPath  = is_array( $asPath ) ? $asPath : array( $asPath );
        $_aPath  = array_values( $_aPath );
        $_sPath  = array_shift( $_aPath );
        return file_exists( $_sPath ) 
            ? trim( 
                implode( 
                    "", 
                    array_slice( 
                        file( $_sPath ), 
                        - $iLines 
                    ) 
                ) 
            )
            : '';
        
    }                 
    
    /**
     * Sanitizes the given file name.
     * 
     * @since       3.4.6
     */
    static public function sanitizeFileName( $sFileName, $sReplacement='_' ) {
        
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;:[]().        
        $sFileName = preg_replace( "([^\w\s\d\-_~,;:\[\]\(\).])", $sReplacement, $sFileName );
        
        // Remove any runs of periods.
        return preg_replace( "([\.]{2,})", '', $sFileName );
        
    }
          
}