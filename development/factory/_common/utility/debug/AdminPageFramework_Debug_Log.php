<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
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
        $_sCallerFunction   = self::___getCallerFunctionName( $_oCallerInfo );
        $_sCallerClass      = self::___getCallerClassName( $_oCallerInfo );
        $_fCurrentTimeStamp = microtime( true );

        file_put_contents(
            self::___getLogFilePath( $sFilePath, $_sCallerClass ),
            self::___getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction ),
            FILE_APPEND
        );

        $_fPreviousTimeStamp = $_fCurrentTimeStamp;

    }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function ___getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction ) {
            return self::___getLogHeadingLine(
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
        static private function ___getCallerFunctionName( $oCallerInfo ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 2, 'function' ), // key
                ''      // default
            );
        }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function ___getCallerClassName( $oCallerInfo ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 2, 'class' ), // key
                ''      // default
            );
        }
        /**
         * Determines the log file path.
         * @since       3.5.3
         * @sicne       3.8.19  Shortened the generated file name.
         * @internal
         * @return      string      The path of the file to log the contents.
         * @param       string|boolean  $bsFilePath     If the file path is specified, use that path. Otherwise, generate a file path name.
         */
        static private function ___getLogFilePath( $bsFilePath, $sCallerClass ) {

            $_bFileExists = self::___createFile( $bsFilePath );
            if ( $_bFileExists ) {
                return $bsFilePath;
            }
            // Return a generated default log path.
            $_sWPContentDir  = WP_CONTENT_DIR . DIRECTORY_SEPARATOR;
            $_sClassBaseName = $sCallerClass
                ? basename( $sCallerClass )
                : basename( get_class() );
            return $_sWPContentDir . $_sClassBaseName . '_' . date( "Ymd" ) . '.log';

        }
            /**
             * Creates a file.
             * @return      boolean
             * @internal
             */
            static private function ___createFile( $sFilePath ) {
                if ( ! $sFilePath || true === $sFilePath ) {
                    return false;
                }
                if ( file_exists( $sFilePath ) ) {
                    return true;
                }
                // Otherwise, create a file.
                $_bhResource = fopen( $sFilePath, 'w' );
                return ( boolean ) $_bhResource;
            }

        /**
         * Returns the heading part of a log item.
         * @since       3.5.3
         * @internal
         * @return      string      the heading part of a log item.
         */
        static private function ___getLogHeadingLine( $fCurrentTimeStamp, $nElapsed, $sCallerClass, $sCallerFunction ) {

            $_nNow              = $fCurrentTimeStamp + ( self::___getSiteGMTOffset() * 60 * 60 );
            $_nMicroseconds     = str_pad( round( ( $_nNow - floor( $_nNow ) ) * 10000 ), 4, '0' );
            $_aOutput           = array(
                date( "Y/m/d H:i:s", $_nNow ) . '.' . $_nMicroseconds,
                self::___getFormattedElapsedTime( $nElapsed ),
                self::___getPageLoadID(),
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
            static private function ___getSiteGMTOffset() {
                static $_nGMTOffset;
                $_nGMTOffset        = isset( $_nGMTOffset )
                    ? $_nGMTOffset
                    : get_option( 'gmt_offset' );
                return $_nGMTOffset;
            }

            /**
             * @return      integer
             */
            static private function ___getPageLoadID() {
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
            static private function ___getFormattedElapsedTime( $nElapsed ) {

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
