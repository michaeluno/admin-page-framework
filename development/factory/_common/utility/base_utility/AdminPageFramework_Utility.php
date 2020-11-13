<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since       2.0.0
 * @since       3.7.3       Became not abstract for the xdebug max nesting level fatal error workaround.
 * @extends     AdminPageFramework_Utility_SystemInformation
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_Utility extends AdminPageFramework_Utility_HTMLAttribute {

    /**
     * @param  array   $aRequest
     * @param  boolean $bStripSlashes   Whether to fix magic quotes.
     * @return array
     * @since  3.8.24
     */
    static public function getHTTPRequestSanitized( array $aRequest, $bStripSlashes ) {
        foreach( $aRequest as $_isIndex => $_mValue ) {
            if ( is_array( $_mValue ) ) {
                $aRequest[ $_isIndex ] = self::getHTTPRequestSanitized( $_mValue, $bStripSlashes );
                continue;
            }
            if ( is_string( $_mValue ) ) {
                $aRequest[ $_isIndex ] = _sanitize_text_fields( $_mValue, true );
            }
        }
        return $bStripSlashes ? stripslashes_deep( $aRequest ) : $aRequest;
    }

    /**
     * @var   array
     * @since 3.8.24
     */
    static private $___aObjectCache = array();
    /**
     * @param string|array $asName  If array, it represents a multi-dimensional keys.
     * @param mixed        $mValue
     * @since  3.8.24
     */
    static public function setObjectCache( $asName, $mValue ) {
        self::setMultiDimensionalArray( self::$___aObjectCache, self::getAsArray( $asName ), $mValue );
    }

    /**
     * @param array|string $asName
     * @since  3.8.24
     */
    static public function unsetObjectCache( $asName ) {
        self::unsetDimensionalArrayElement( self::$___aObjectCache, self::getAsArray( $asName ) );
    }

    /**
     * Caches values in the class property.
     *
     * @remark The stored data will be gone after the page load.
     * @param  array|string $asName The key of the object cache array. If an array is given, it represents the multi-dimensional keys.
     * @param  mixed $mDefault
     * @return mixed
     * @since  3.8.24
     */
    static public function getObjectCache( $asName, $mDefault=null ) {
        return self::getArrayValueByArrayKeys( self::$___aObjectCache, self::getAsArray( $asName ), $mDefault );
    }

    /**
     * Shows a message for a deprecated item.
     *
     * Uses the `E_USER_NOTICE` error level so that the message won't be shown if `WP_DEBUG` is `false`.
     *
     * @remark  This method is overridden by the `AdminPageFramework_FrameworkUtility` class.
     * @param   string $sDeprecated
     * @param   string $sAlternative
     * @param   string $sProgramName
     * @since   3.8.8
     */
    static public function showDeprecationNotice( $sDeprecated, $sAlternative='', $sProgramName='Admin Page Framework' ) {
        trigger_error(
            $sProgramName . ': ' . sprintf(
                $sAlternative
                    ? '<code>%1$s</code> has been deprecated. Use <code>%2$s</code> instead.'
                    : '<code>%1$s</code> has been deprecated.',
                $sDeprecated, // %1$s
                $sAlternative // %2%s
            ),
            E_USER_NOTICE
        );
    }

    /**
     * Calls back a user defined function.
     *
     * This is meant to be used to filter a value using a callback. When a callback is not available, the first parameter element will be returned.
     * so set a default return value to the first element of the parameter array.
     *
     * @since       3.7.0
     * @since       3.8.5               Moved from `AdminPageFramework_Form_Base`. Added the default value to the `$asParameters`second parameter.
     * @param       callable            $oCallable
     * @param       string|array        $asParameters       Parameters to pass to the callback function.
     * @return      mixed
     */
    public function callBack( $oCallable, $asParameters=array() ) {
        $_aParameters   = self::getAsArray(
            $asParameters,
            true // preserve empty
        );
        $_mDefaultValue = self::getElement( $_aParameters, 0 );
        return is_callable( $oCallable )
            ? call_user_func_array( $oCallable, $_aParameters )
            : $_mDefaultValue;
    }

    /**
     * Checks if the given id (usually a function name) has been called throughout the page load.
     *
     * This is used to check if a function which needs to be done only once has been already called or not.
     *
     * @since       3.7.0
     * @param       string  $sKey   The string identifier of the call.
     * @return      boolean
     */
    static public function hasBeenCalled( $sKey ) {
        if ( isset( self::$___aCallStack[ $sKey ] ) ) {
            return true;
        }
        self::$___aCallStack[ $sKey ] = true;
        return false;
    }
        /**
         * Stores calls.
         * @internal
         */
        static private $___aCallStack = array();

    /**
     * Captures the output buffer of the given function.
     * @since       3.6.3
     * @param       callable    $cCallable
     * @param       array       $aParameters
     * @return      string      The captured output buffer.
     */
    static public function getOutputBuffer( $cCallable, array $aParameters=array() ) {

        ob_start();
        echo call_user_func_array( $cCallable, $aParameters );
        $_sContent = ob_get_contents();
        ob_end_clean();
        return $_sContent;

    }

    /**
     * Generates brief object information.
     *
     * @remark      Meant to be used for the `__toString()` method.
     * @since       3.6.0
     * @param       object  $oInstance
     * @return      string
     */
    static public function getObjectInfo( $oInstance ) {

        $_iCount     = count( get_object_vars( $oInstance ) );
        $_sClassName = get_class( $oInstance );
        return '(object) ' . $_sClassName . ': ' . $_iCount . ' properties.';

    }


    /**
     * Returns one or the other.
     *
     * Saves one conditional statement.
     *
     * @remark      Use this only when the performance is not critical.
     * @since       3.5.3
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mValue     The value to evaluate.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mTrue      The value to return when the first parameter value yields true.
     * @param       boolean|integer|double|string|array|object|resource|NULL        $mFalse     The value to return when the first parameter value yields false.
     * @return      mixed
     */
    static public function getAOrB( $mValue, $mTrue=null, $mFalse=null ) {
        return $mValue ? $mTrue : $mFalse;
    }

}
