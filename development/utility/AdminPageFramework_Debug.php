<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */
if ( ! class_exists( 'AdminPageFramework_Debug' ) ) :
/**
 * Provides debugging methods.
 *
 * @since 2.0.0
 * @since 3.1.3 Extends AdminPageFramework_WPUtility
 * @package AdminPageFramework
 * @subpackage Debug
 * @internal
 */
class AdminPageFramework_Debug extends AdminPageFramework_WPUtility {
            
    /**
     * Prints out the given array contents
     * 
     * If a file pass is given, it saves the output in the file.
     * 
     * @remark      An alias of the dumpArray() method.
     * @since       3.2.0
     */
    static public function dump( $asArray, $sFilePath=null ) {
        echo self::getArray( $asArray, $sFilePath );
    }    
        /**
         * Prints out the given array contents
         * 
         * If a file pass is given, it saves the output in the file.
         * 
         * @since unknown
         * @deprecated      3.2.0
         */
        static public function dumpArray( $asArray, $sFilePath=null ) {
            self::dump( $asArray, $sFilePath );
        }    
        
    /**
     * Retrieves the output of the given array contents.
     * 
     * If a file pass is given, it saves the output in the file.
     * 
     * @remark      An alias of getArray() method.
     * @since       3.2.0
     */
    static public function get( $asArray, $sFilePath=null, $bEscape=true ) {

        if ( $sFilePath ) self::logArray( $asArray, $sFilePath );     
        
        return $bEscape
            ? "<pre class='dump-array'>" . htmlspecialchars( print_r( $asArray, true ) ) . "</pre>" // esc_html() has a bug that breaks with complex HTML code.
            : print_r( $asArray, true ); // non-escape is used for exporting data into file.    
        
    }
        /**
         * Retrieves the output of the given array contents.
         * 
         * If a file pass is given, it saves the output in the file.
         * 
         * @since       2.1.6 The $bEncloseInTag parameter is added.
         * @since       3.0.0 Changed the $bEncloseInTag parameter to bEscape.
         * @deprecated` 3.2.0
         */
        static public function getArray( $asArray, $sFilePath=null, $bEscape=true ) {
            return self::get( $asArray, $sFilePath, $bEscape );
        }      
            
    /**
     * Logs the given variable output to a file.
     * 
     * @remark The alias of the logArray() method.
     * @since 3.1.0
     * @since 3.1.3 Made it leave milliseconds and elapsed time from the last call of the method.
     **/
    static public function log( $v, $sFilePath=null ) {
                
        static $_iPageLoadID; // identifies the page load.
        static $_nGMTOffset;
        static $_fPreviousTimeStamp = 0;
        $_iPageLoadID = $_iPageLoadID ? $_iPageLoadID : uniqid();     
        $_oCallerInfo = debug_backtrace();
        $_sCallerFunction = isset( $_oCallerInfo[ 1 ]['function'] ) ? $_oCallerInfo[ 1 ]['function'] : '';
        $_sCallerClasss = isset( $_oCallerInfo[ 1 ]['class'] ) ? $_oCallerInfo[ 1 ]['class'] : '';
        $sFilePath             = ! $sFilePath
            ? WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . $_sCallerClasss . '_' . date( "Ymd" ) . '.log'
            : ( true === $sFilePath
                ? WP_CONTENT_DIR . DIRECTORY_SEPARATOR . get_class() . '_' . date( "Ymd" ) . '.log'
                : $sFilePath
            );
        $_nGMTOffset = isset( $_nGMTOffset ) ? $_nGMTOffset : get_option( 'gmt_offset' );
        $_fCurrentTimeStamp = microtime( true );
        $_nNow = $_fCurrentTimeStamp + ( $_nGMTOffset * 60 * 60 );
        $_nMicroseconds = round( ( $_nNow - floor( $_nNow ) ) * 10000 );
        $_nMicroseconds = str_pad( $_nMicroseconds, 4, '0' );
        $_nElapsed = round( $_fCurrentTimeStamp - $_fPreviousTimeStamp, 3 );
        $_aElapsedParts = explode( ".", ( string ) $_nElapsed );
        $_sElapsedFloat = str_pad( isset( $_aElapsedParts[ 1 ] ) ? $_aElapsedParts[ 1 ] : 0, 3, '0' );
        $_sElapsed = isset( $_aElapsedParts[ 0 ] ) ? $_aElapsedParts[ 0 ] : 0;
        $_sElapsed = strlen( $_sElapsed ) > 1 ? '+' . substr( $_sElapsed, -1, 2 ) : ' ' . $_sElapsed;
        $_sHeading = date( "Y/m/d H:i:s", $_nNow ) . '.' . $_nMicroseconds . ' ' 
            . $_sElapsed . '.' . $_sElapsedFloat . ' '    
            . "{$_iPageLoadID} {$_sCallerClasss}::{$_sCallerFunction} " 
            . current_filter() . ' '
            . self::getCurrentURL();
        file_put_contents( 
            $sFilePath, 
            $_sHeading . PHP_EOL . print_r( $v, true ) . PHP_EOL . PHP_EOL,
            FILE_APPEND 
        );     
        $_fPreviousTimeStamp = $_fCurrentTimeStamp;
        
    }     
        /**
         * Logs the given array output into the given file.
         * 
         * @since       2.1.1
         * @since       3.0.3 Changed the default log location and file name.
         * @deprecated  3.1.0 Use the log() method instead
         */
        static public function logArray( $asArray, $sFilePath=null ) {
            self::log( $asArray, $sFilePath );     
        }        
}
endif;