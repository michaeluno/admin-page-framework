<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods dealing with file paths which do not use WordPress functions.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility_Array
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_Path extends AdminPageFramework_Utility_Array {
    
    /**
     * Calculates the relative path from the given path.
     * 
     * This function is used to generate a template path.
     * 
     * @since 2.1.5
     * @author Gordon
     * @author Michael Uno, Modified variable names and spacing.
     * @see http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
     */
    static public function getRelativePath( $from, $to ) {
        
        // some compatibility fixes for Windows paths
        $from = is_dir( $from ) ? rtrim( $from, '\/') . '/' : $from;
        $to   = is_dir( $to )   ? rtrim( $to, '\/') . '/'   : $to;
        $from = str_replace( '\\', '/', $from );
        $to   = str_replace( '\\', '/', $to );

        $from     = explode( '/', $from );
        $to       = explode( '/', $to );
        $relPath  = $to;

        foreach( $from as $depth => $dir ) {
            // find first non-matching dir
            if( $dir === $to[ $depth ] ) {
                // ignore this directory
                array_shift( $relPath );
            } else {
                // get number of remaining dirs to $from
                $remaining = count( $from ) - $depth;
                if( $remaining > 1 ) {
                    // add traversals up to first matching dir
                    $padLength = ( count( $relPath ) + $remaining - 1 ) * -1;
                    $relPath = array_pad( $relPath, $padLength, '..' );
                    break;
                } else {
                    $relPath[ 0 ] = './' . $relPath[ 0 ];
                }
            }
        }
        return implode( '/', $relPath );
        
    }
    
    /**
     * Attempts to find the caller scrip path.
     * 
     * @since 3.0.0
     * @return string
     */
    static public function getCallerScriptPath( $asRedirectedFiles=array( __FILE__ ) ) {

        $aRedirectedFiles = ( array ) $asRedirectedFiles;
        $aRedirectedFiles[] = __FILE__;
        $_sCallerFilePath = '';
        foreach( debug_backtrace() as $aDebugInfo )  {     
            $_sCallerFilePath = $aDebugInfo['file'];
            if ( in_array( $_sCallerFilePath, $aRedirectedFiles ) ) { continue; }
            break; // catch the first found item.
        }
        return $_sCallerFilePath;
        
    }    
        
}