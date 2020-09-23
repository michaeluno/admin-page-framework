<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
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
     * @param       mixed       $mValue             The value to log.
     * @param       string      $sFilePath          The log file path.
     * @param       boolean     $bStackTrace        Whether to include the stack trace.
     * @param       integer     $iTrace             How many times to climb the backtrace.
     * @param       integer     $iStringLengthLimit The string value length limit.
     * @param       integer     $iArrayDepthLimit   The depth limit for arrays.
     * @since       3.8.9
     * @return      void
     **/
    static protected function _log( $mValue, $sFilePath=null, $bStackTrace=false, $iTrace=0, $iStringLengthLimit=99999, $iArrayDepthLimit=50 ) {

        static $_fPreviousTimeStamp = 0;

        $_oCallerInfo       = debug_backtrace();
        $_sCallerFunction   = self::___getCallerFunctionName( $_oCallerInfo, $iTrace );
        $_sCallerClass      = self::___getCallerClassName( $_oCallerInfo, $iTrace );
        $_fCurrentTimeStamp = microtime( true );
        $_sLogContent       = self::___getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction, $iStringLengthLimit, $iArrayDepthLimit )
            . ( $bStackTrace ? self::getStackTrace($iTrace + 1 ) : '' )
            . PHP_EOL;

        file_put_contents( self::___getLogFilePath( $sFilePath, $_sCallerClass ), $_sLogContent, FILE_APPEND );
        $_fPreviousTimeStamp = $_fCurrentTimeStamp;

    }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function ___getLogContents( $mValue, $_fCurrentTimeStamp, $_fPreviousTimeStamp, $_sCallerClass, $_sCallerFunction, $iStringLengthLimit, $iArrayDepthLimit ) {
            return self::___getLogHeadingLine(
                    $_fCurrentTimeStamp,
                    round( $_fCurrentTimeStamp - $_fPreviousTimeStamp, 3 ), // elapsed time
                    $_sCallerClass,
                    $_sCallerFunction
                ) . PHP_EOL
                . self::_getLegibleDetails( $mValue, $iStringLengthLimit, $iArrayDepthLimit ) . PHP_EOL;
        }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function ___getCallerFunctionName( $oCallerInfo, $iTrace ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 2 + $iTrace, 'function' ), // key
                ''      // default
            );
        }
        /**
         * @since       3.8.9
         * @return      string
         */
        static private function ___getCallerClassName( $oCallerInfo, $iTrace ) {
            return self::getElement(
                $oCallerInfo,  // subject array
                array( 2 + $iTrace, 'class' ), // key
                ''      // default
            );
        }
        /**
         * Determines the log file path.
         * @since       3.5.3
         * @sicne       3.8.19  Shortened the generated file name.
         * @internal
         * @return      string      The path of the file to log the contents.
         * @param       string|boolean  $bsFilePathOrName     If the file path is specified, use that path. If a string of non-path value is given, it will be used as a part of the log file name. Otherwise, automatically generates a file name with a caller class name.
         */
        static private function ___getLogFilePath( $bsFilePathOrName, $sCallerClass ) {

            $_sWPContentDir  = WP_CONTENT_DIR . DIRECTORY_SEPARATOR;

            // It is a partial file name
            if ( is_string( $bsFilePathOrName ) && ! self::hasSlash( $bsFilePathOrName ) ) {
                return $_sWPContentDir . $bsFilePathOrName . '_' . date( "Ymd" ) . '.log';
            }

            // Try creating a file.
            $_bFileExists = self::___createFile( $bsFilePathOrName );
            if ( $_bFileExists ) {
                return $bsFilePathOrName;
            }

            // At this point, the file creation failed.

            // Return a generated default log path.
            $_sClassBaseName = $sCallerClass ? basename( $sCallerClass ) : basename( get_class() );
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
                static $_sPageLoadID;
                $_sPageLoadID       = $_sPageLoadID
                    ? $_sPageLoadID
                    : uniqid();
                return $_sPageLoadID;
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
