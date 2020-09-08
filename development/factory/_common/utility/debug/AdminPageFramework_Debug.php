<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides debugging methods.
 *
 * Use the methods of this class to check variable contents.
 *
 * @image           http://admin-page-framework.michaeluno.jp/image/common/utility/debug.png
 * @since           2.0.0
 * @since           3.1.3       Extends AdminPageFramework_WPUtility
 * @since           3.7.1       Extends AdminPageFramework_FrameworkUtility
 * @extends         AdminPageFramework_FrameworkUtility
 * @package         AdminPageFramework/Common/Utility
 */
class AdminPageFramework_Debug extends AdminPageFramework_Debug_Log {

    /**
     * @param Exception $oException
     * @param integer $iSkip    The number of skipping records. This is used when the caller does not want to include the self function/method.
     *
     * @return  string
     * @since   3.8.22
     * @see https://stackoverflow.com/questions/1949345/how-can-i-get-the-full-string-of-php-s-gettraceasstring/6076667#6076667
     */
    static public function getStackTrace( Exception $oException, $iSkip=0 ) {

        $_sTrace     = "";
        $_iCount     = 0;
        foreach ( $oException->getTrace() as $_iIndex => $_aFrame ) {

            if ( $iSkip > $_iIndex ) {
                continue;
            }
            $_aFrame     = $_aFrame + array(
                'file'  => null, 'line' => null, 'function' => null,
                'class' => null, 'args' => array(),
            );
            $_sArguments = self::___getArgumentsOfEachStackTrace( $_aFrame[ 'args' ] );
            $_sTrace    .= sprintf(
                "#%s %s(%s): %s(%s)\n",
                $_iCount,
                $_aFrame[ 'file' ],
                $_aFrame[ 'line' ],
                isset( $_aFrame[ 'class' ] ) ? $_aFrame[ 'class' ] . '->' . $_aFrame[ 'function' ] : $_aFrame[ 'function' ],
                $_sArguments
            );
            $_iCount++;

        }
        return $_sTrace;

    }
        /**
         * @param array $aTraceArguments
         * @return string
         * @since   3.8.22
         * @internal
         */
        static private function ___getArgumentsOfEachStackTrace( array $aTraceArguments ) {

            $_aArguments = array();
            foreach ( $aTraceArguments as $_mArgument ) {
                $_sType        = gettype( $_mArgument );
                $_sType        = str_replace(
                    array( 'resource (closed)', 'unknown type', 'integer', 'double', ),
                    array( 'resource', 'unknown', 'scalar', 'scalar', ),
                    $_sType
                );
                $_sMethodName  = "___getStackTraceArgument_{$_sType}";
                $_aArguments[] = method_exists( __CLASS__, $_sMethodName )
                    ? self::{$_sMethodName}( $_mArgument )
                    : $_sType;
            }
            return join(", ",  $_aArguments );
        }
            /**
             * @since   3.8.22
             * @param mixed $mArgument
             * @internal
             * @return string
             */
            static private function ___getStackTraceArgument_string( $mArgument ) {
                return "'" . $mArgument . "'";
            }
            static private function ___getStackTraceArgument_scalar( $mArgument ) {
                return $mArgument;
            }
            static private function ___getStackTraceArgument_boolean( $mArgument ) {
                return ( $mArgument ) ? "true" : "false";
            }
            static private function ___getStackTraceArgument_NULL( $mArgument ) {
                return 'NULL';
            }
            static private function ___getStackTraceArgument_object( $mArgument ) {
                return 'Object(' . get_class( $mArgument ) . ')';
            }
            static private function ___getStackTraceArgument_resource( $mArgument ) {
                return get_resource_type( $mArgument );
            }
            static private function ___getStackTraceArgument_unknown( $mArgument ) {
                return gettype( $mArgument );
            }
            static private function ___getStackTraceArgument_array( $mArgument ) {
                $_sOutput = '';
                $_iMax    = 10;
                $_iTotal  = count( $mArgument );
                $_iIndex  = 0;
                foreach( $mArgument as $_sKey => $_mValue ) {
                    $_iIndex++;
                    $_mValue   = is_scalar( $_mValue )
                        ? $_mValue
                        : ucfirst( gettype( $_mValue ) ) . (
                            is_object( $_mValue )
                                ? ' (' . get_class( $_mValue ) . ')'
                                : ''
                        );
                    $_sOutput .= $_sKey . ': ' . $_mValue . ',';
                    if ( $_iIndex > $_iMax && $_iTotal > $_iMax ) {
                        $_sOutput  = rtrim( $_sOutput, ','  ) . '...';
                        break;
                    }
                }
                $_sOutput = rtrim( $_sOutput, ',' );
                return "Array({$_sOutput})";
            }
    /**
     * Prints out the given variable contents
     *
     * If a file pass is given to the second parameter, it saves the output in the file.
     *
     * @since       3.2.0
     * @remark      An alias of the dumpArray() method.
     * @param       array|string    $asArray        The variable to check its contents.
     * @param       string          $sFilePath      The file path for a log file.
     * @return      void
     */
    static public function dump( $asArray, $sFilePath=null ) {
        echo self::get( $asArray, $sFilePath );
    }

    /**
     * Returns a string representation of a given value with details.
     * @since       3.8.9
     * @return      string
     */
    static public function getDetails( $mValue, $bEscape=true, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {
        $_sValueWithDetails = self::_getArrayRepresentationSanitized(
            self::_getLegibleDetails( $mValue, $iStringLengthLimit, $iArrayDepthLimit )
        );
        return $bEscape
            ? "<pre class='dump-array'>"
                    . htmlspecialchars( $_sValueWithDetails )
                . "</pre>"
            : $_sValueWithDetails; // non-escape is used for exporting data into file.
    }

    /**
     * Retrieves the output of the given variable contents.
     *
     * If a file pass is given to the second parameter, it saves the output in the file.
     *
     * To get variable details, use `getDetails()`.
     * @see AdminPageFramework_Debug::getDetails()
     * @remark      An alias of getArray() method. No variable details.
     * @since       3.2.0
     * @param       array|string    $asArray            The variable to check its contents.
     * @param       string          $sFilePath          The file path for a log file.
     * @param       boolean         $bEscape            Whether to escape characters.
     * @param       integer         $iStringLengthLimit
     * @param       integer         $iArrayDepthLimit
     * @return      string
     */
    static public function get( $asArray, $sFilePath=null, $bEscape=true, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {

        if ( $sFilePath ) {
            self::log( $asArray, $sFilePath );
        }
        return $bEscape
            ? "<pre class='dump-array'>"
                    . htmlspecialchars( self::_getLegible( $asArray, $iStringLengthLimit, $iArrayDepthLimit ) ) // `esc_html()` breaks with complex HTML code.
                . "</pre>"
            : self::_getLegible( $asArray, $iStringLengthLimit, $iArrayDepthLimit ); // non-escape is used for exporting data into file.

    }

    /**
     * Logs the given variable output to a file.
     *
     * <h4>Example</h4>
     * <code>
     * $_aValues = array( 'foo', 'bar' );
     * AdminPageFramework_Debug::log( $aValues );
     * </code>
     *
     * @remark      The alias of the `logArray()` method.
     * @since       3.1.0
     * @since       3.1.3       Made it leave milliseconds and elapsed time from the last call of the method.
     * @since       3.3.0       Made it indicate the data type.
     * @since       3.3.1       Made it indicate the data length.
     * @since       3.8.22      Added the `$iTrace` parameter.
     * @param       mixed       $mValue         The value to log.
     * @param       string      $sFilePath      The log file path.
     * @param       integer     $iTrace         The count of back-trace.
     * @param       integer     $iStringLengthLimit The string value length limit.
     * @param       integer     $iArrayDepthLimit   The depth limit for arrays.*
     * @return      void
     **/
    static public function log( $mValue, $sFilePath=null, $iTrace=0, $iStringLengthLimit=99999, $iArrayDepthLimit=50 ) {
        self::_log( $mValue, $sFilePath, $iTrace, $iStringLengthLimit, $iArrayDepthLimit );
    }

    /* Deprecated Methods */

    /**
     * Prints out the given variable contents.
     *
     * If a file pass is given, it saves the output in the file.
     *
     * @since unknown
     * @deprecated      3.2.0
     */
    static public function dumpArray( $asArray, $sFilePath=null ) {
        self::showDeprecationNotice( 'AdminPageFramework_Debug::' . __FUNCTION__, 'AdminPageFramework_Debug::dump()' );
        AdminPageFramework_Debug::dump( $asArray, $sFilePath );
    }

    /**
     * Retrieves the output of the given array contents.
     *
     * If a file pass is given, it saves the output in the file.
     *
     * @since       2.1.6 The $bEncloseInTag parameter is added.
     * @since       3.0.0 Changed the $bEncloseInTag parameter to bEscape.
     * @deprecated  3.2.0
     */
    static public function getArray( $asArray, $sFilePath=null, $bEscape=true ) {
        self::showDeprecationNotice( 'AdminPageFramework_Debug::' . __FUNCTION__, 'AdminPageFramework_Debug::get()' );
        return AdminPageFramework_Debug::get( $asArray, $sFilePath, $bEscape );
    }

    /**
     * Logs the given array output into the given file.
     *
     * @since       2.1.1
     * @since       3.0.3   Changed the default log location and file name.
     * @deprecated  3.1.0   Use the `log()` method instead.
     */
    static public function logArray( $asArray, $sFilePath=null ) {
        self::showDeprecationNotice( 'AdminPageFramework_Debug::' . __FUNCTION__, 'AdminPageFramework_Debug::log()' );
        AdminPageFramework_Debug::log( $asArray, $sFilePath );
    }

    /**
     * Returns a string representation of the given value.
     * @since       3.5.0
     * @param       mixed       $mValue     The value to get as a string
     * @internal
     * @return      string
     * @deprecated  3.8.9
     */
    static public function getAsString( $mValue ) {
        self::showDeprecationNotice( 'AdminPageFramework_Debug::' . __FUNCTION__ );
        return self::_getLegible( $mValue );
    }


}
