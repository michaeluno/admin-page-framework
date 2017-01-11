<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2017 Michael Uno; Licensed MIT
 * 
 */

/**
 * A base class of the debug class.
 *
 * @since           3.8.9
 * @extends         AdminPageFramework_Debug_Base
 * @package         AdminPageFramework/Common/Utility
 */
class AdminPageFramework_Debug_Log extends AdminPageFramework_Debug_Base {

    /**
     * Logs the given variable output to a file.
     * 
     * @param       mixed       $mValue         The value to log.  
     * @param       string      $sFilePath      The log file path.
     * @since       3.8.9
     * @return      void
     **/
    static protected function _log( $mValue, $sFilePath=null ) {
                
        static $_fPreviousTimeStamp = 0;
        
        $_oCallerInfo       = debug_backtrace();
        $_sCallerFunction   = self::_getCallerFunctionName( $_oCallerInfo );
        $_sCallerClass      = self::_getCallerClassName( $_oCallerInfo );
        $_fCurrentTimeStamp = microtime( true );
        
        file_put_contents( 
            self::_getLogFilePath( $sFilePath, $_sCallerClass ), 
            self::_getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction ),
            FILE_APPEND 
        );     
        
        $_fPreviousTimeStamp = $_fCurrentTimeStamp;
        
    }   
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function _getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction ) {
            return self::_getLogHeadingLine( 
                    $_fCurrentTimeStamp,
                    round( $_fCurrentTimeStamp - $_fPreviousTimeStamp, 3 ), // elapsed time
                    $_sCallerClass,
                    $_sCallerFunction
                ) . PHP_EOL
                . self::_getLegibleDetails( $mValue ) . PHP_EOL . PHP_EOL;
        }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function _getCallerFunctionName( $oCallerInfo ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 1, 'function' ), // key
                ''      // default
            );
        }
        /**
         * @since       3.8.9
         * @return      string
         */        
        static private function _getCallerClassName( $oCallerInfo ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 1, 'class' ), // key
                ''      // default
            );           
        }
        /**
         * Determines the log file path.
         * @since       3.5.3 
         * @internal    
         * @return      string      The path of the file to log the contents.
         */
        static private function _getLogFilePath( $bsFilePath, $sCallerClass ) {
        
            $_bFileExists = self::_createFile( $bsFilePath );
            if ( $_bFileExists ) {
                return $bsFilePath;
            }
            // Return a generated default log path.
            if ( true === $bsFilePath ) {
                return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . basename( get_class() ) . '_' . date( "Ymd" ) . '.log';
            }
            return WP_CONTENT_DIR . DIRECTORY_SEPARATOR . basename( get_class() ) . '_' . basename( $sCallerClass ) . '_' . date( "Ymd" ) . '.log';
            
        }
            /**
             * Creates a file.
             * @return      boolean
             * @internal
             */
            static private function _createFile( $sFilePath ) {
                if ( ! $sFilePath || true === $sFilePath ) {
                    return false;
                }
                if ( file_exists( $sFilePath ) ) {
                    return true;
                }
                // Otherwise, create a file.
                $_bhResrouce = fopen( $sFilePath, 'w' );
                return ( boolean ) $_bhResrouce;                
            }

        /**
         * Returns the heading part of a log item.
         * @since       3.5.3
         * @internal
         * @return      string      the heading part of a log item.
         */
        static private function _getLogHeadingLine( $fCurrentTimeStamp, $nElapsed, $sCallerClass, $sCallerFunction ) {
            
            $_nNow              = $fCurrentTimeStamp + ( self::_getSiteGMTOffset() * 60 * 60 );
            $_nMicroseconds     = str_pad( round( ( $_nNow - floor( $_nNow ) ) * 10000 ), 4, '0' );            
            $_aOutput           = array(
                date( "Y/m/d H:i:s", $_nNow ) . '.' . $_nMicroseconds,
                self::_getFormattedElapsedTime( $nElapsed ),
                self::_getPageLoadID(),
                self::getFrameworkVersion(),
                $sCallerClass . '::' . $sCallerFunction,
                current_filter(),
                self::getCurrentURL(),
            );
            return implode( ' ', $_aOutput );         
            
        }
    
            /**
             * Returns the GMT offset of the site.
             * 
             * @return      numeric
             */
            static private function _getSiteGMTOffset() {
                static $_nGMTOffset;
                $_nGMTOffset        = isset( $_nGMTOffset ) 
                    ? $_nGMTOffset 
                    : get_option( 'gmt_offset' );          
                return $_nGMTOffset;
            }
            
            /**
             * @return      integer
             */
            static private function _getPageLoadID() {
                static $_iPageLoadID;
                $_iPageLoadID       = $_iPageLoadID 
                    ? $_iPageLoadID 
                    : uniqid();                
                return $_iPageLoadID;
            }
        
            /**
             * Returns formatted elapsed time.
             * @since       3.5.3
             * @internal
             * @return      string      Formatted elapsed time.
             */
            static private function _getFormattedElapsedTime( $nElapsed ) {
                
                $_aElapsedParts     = explode( ".", ( string ) $nElapsed );
                $_sElapsedFloat     = str_pad(
                    self::getElement(
                        $_aElapsedParts, // subject array
                        1, // key
                        0  // default
                    ),      
                    3, 
                    '0'
                );
                $_sElapsed          = self::getElement(
                    $_aElapsedParts,  // subject array
                    0,  // key
                    0   // default
                );                                   
                $_sElapsed          = strlen( $_sElapsed ) > 1 
                    ? '+' . substr( $_sElapsed, -1, 2 ) 
                    : ' ' . $_sElapsed;
                return $_sElapsed . '.' . $_sElapsedFloat;
            
            }
    
}
