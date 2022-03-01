<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
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
     * Prints out the given variable contents
     *
     * If a file pass is given to the second parameter, it saves the output in the file.
     *
     * @since       3.2.0
     * @remark      An alias of the dumpArray() method.
     * @param       array|string    $asArray        The variable to check its contents.
     * @param       string          $sFilePath      The file path for a log file.
     * @param       boolean         $bStackTrace    Whether to include a stack trace.
     * @param       integer         $iStringLengthLimit
     * @param       integer         $iArrayDepthLimit
     * @return      void
     */
    static public function dump( $asArray, $sFilePath=null, $bStackTrace=false, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {
        echo self::get( $asArray, $sFilePath, true, $bStackTrace, $iStringLengthLimit, $iArrayDepthLimit );
    }

    /**
     * Returns a string representation of a given value with details.
     * @since       3.8.9
     * @return      string
     */
    static public function getDetails( $mValue, $bEscape=true, $bStackTrace=false, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {
        $_sValueWithDetails = self::getArrayRepresentationSanitized(
            self::_getLegibleDetails( $mValue, $iStringLengthLimit, $iArrayDepthLimit )
        );
        $_sValueWithDetails = $bStackTrace
            ? $_sValueWithDetails . PHP_EOL . self::getStackTrace()
            : $_sValueWithDetails;
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
     * @param       boolean         $bStackTrace        Whether to include a stack trace.
     * @param       integer         $iStringLengthLimit
     * @param       integer         $iArrayDepthLimit
     * @return      string
     */
    static public function get( $asArray, $sFilePath=null, $bEscape=true, $bStackTrace=false, $iStringLengthLimit=0, $iArrayDepthLimit=0 ) {

        if ( $sFilePath ) {
            self::log( $asArray, $sFilePath );
        }
        $_sContent = self::_getLegible( $asArray, $iStringLengthLimit, $iArrayDepthLimit )
            . (
                $bStackTrace
                    ? PHP_EOL . self::getStackTrace()
                    : ''
            );
        return $bEscape
            ? "<pre class='dump-array'>"
                    . htmlspecialchars( $_sContent ) // `esc_html()` breaks with complex HTML code.
                . "</pre>"
            : $_sContent; // non-escape is used for exporting data into file.

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
     * @since       3.8.22      Added the `$bStackTrace`, `$iTrace`, `$iStringLengthLimit`, `$iArrayDepthLimit` parameters.
     * @param       mixed       $mValue         The value to log.
     * @param       string      $sFilePath      The log file path.
     * @param       boolean     $bStackTrace    Whether to include the stack trace.
     * @param       integer     $iTrace         The count of back-trace.
     * @param       integer     $iStringLengthLimit The string value length limit.
     * @param       integer     $iArrayDepthLimit   The depth limit for arrays.*
     * @return      void
     **/
    static public function log( $mValue, $sFilePath=null, $bStackTrace=false, $iTrace=0, $iStringLengthLimit=99999, $iArrayDepthLimit=50 ) {
        self::_log( $mValue, $sFilePath, $bStackTrace, $iTrace, $iStringLengthLimit, $iArrayDepthLimit );
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

}
