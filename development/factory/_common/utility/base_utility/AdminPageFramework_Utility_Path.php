<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods dealing with file paths which do not use WordPress functions.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility_ArraySetter
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_Path extends AdminPageFramework_Utility_ArraySetter {

    /**
     * Calculates the relative path from the given path.
     *
     * This function is used to generate a template path.
     *
     * @since   2.1.5
     * @see     http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
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
     * @since       3.0.0
     * @since       3.7.9       Made the first parameter only accepts a string.
     * @since       3.8.2       deprecated caching results as it caused wrong path as the passed path can be the same for different scripts.
     * @return      string      The found caller file path.
     */
    static public function getCallerScriptPath( $sRedirectedFilePath ) {

        $_aRedirectedFilePaths = array( $sRedirectedFilePath, __FILE__ );
        $_sCallerFilePath      = '';
        $_aBackTrace           = call_user_func_array(
            'debug_backtrace',
            self::_getDebugBacktraceArguments()
        );
        foreach( $_aBackTrace as $_aDebugInfo )  {
            $_sCallerFilePath = $_aDebugInfo[ 'file' ];
            if ( in_array( $_sCallerFilePath, $_aRedirectedFilePaths ) ) {
                continue;
            }
            break; // catch the first found item.
        }
        return $_sCallerFilePath;

    }
        /**
         * @return      array
         * @since       3.8.9
         */
        static private function _getDebugBacktraceArguments() {

            $_aArguments = array(
                defined( 'DEBUG_BACKTRACE_IGNORE_ARGS' )
                    ? DEBUG_BACKTRACE_IGNORE_ARGS
                    : false, // DEBUG_BACKTRACE_PROVIDE_OBJECT for PHP 5.3.6+
                6, // the second parameter: limit
            );

            // The second parameter is only supported in v5.4.0 or above.
            if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
                unset( $_aArguments[ 1 ] );
            }
            return $_aArguments;

        }

}
