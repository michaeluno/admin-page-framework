<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_Utility extends AdminPageFramework_Utility_Interpreter {
    public static function getHTTPRequestSanitized(array $aRequest, $bStripSlashes=true)
    {
        foreach ($aRequest as $_isIndex => $_mValue) {
            if (is_array($_mValue)) {
                $aRequest[ $_isIndex ] = self::getHTTPRequestSanitized($_mValue, $bStripSlashes);
                continue;
            }
            if (is_string($_mValue)) {
                $aRequest[ $_isIndex ] = self::___getHTTPRequestTextValueSanitized($_mValue, $bStripSlashes);
            }
        }
        return $aRequest;
    }
    private static function ___getHTTPRequestTextValueSanitized($sString, $bStripSlashes, $bKeepLineFeeds=true)
    {
        $sString = $bStripSlashes ? stripslashes($sString) : $sString;
        $_sFiltered = wp_check_invalid_utf8($sString);
        if (! $bKeepLineFeeds) {
            $_sFiltered = preg_replace('/[\r\n\t ]+/', ' ', $_sFiltered);
        }
        $_sFiltered = trim($_sFiltered);
        return self::getOctetsRemoved($_sFiltered);
    }
    public static function getOctetsRemoved($sString)
    {
        $_sPattern = '/' . '([ \t\n\r\f]|^)(?!.*:\/\/).*' . '\K' . '%[a-f0-9]{2}' . '(?!\w*%\W)' . '/i';
        $_iPos = 0;
        while (preg_match($_sPattern, $sString, $_aMatches, PREG_OFFSET_CAPTURE, $_iPos)) {
            if (! isset($_aMatches[ 0 ][ 0 ], $_aMatches[ 0 ][ 1 ])) {
                break;
            }
            $_iPos = $_aMatches[ 0 ][ 1 ];
            $sString = substr($sString, 0, $_iPos) . substr($sString, $_iPos + strlen($_aMatches[ 0 ][ 0 ]));
        }
        return $sString;
    }
    private static $___aObjectCache = array();
    public static function setObjectCache($asName, $mValue)
    {
        self::setMultiDimensionalArray(self::$___aObjectCache, self::getAsArray($asName), $mValue);
    }
    public static function unsetObjectCache($asName)
    {
        self::unsetDimensionalArrayElement(self::$___aObjectCache, self::getAsArray($asName));
    }
    public static function getObjectCache($asName, $mDefault=null)
    {
        return self::getArrayValueByArrayKeys(self::$___aObjectCache, self::getAsArray($asName), $mDefault);
    }
    public static function showDeprecationNotice($sDeprecated, $sAlternative='', $sProgramName='Admin Page Framework')
    {
        trigger_error($sProgramName . ': ' . sprintf($sAlternative ? '<code>%1$s</code> has been deprecated. Use <code>%2$s</code> instead.' : '<code>%1$s</code> has been deprecated.', $sDeprecated, $sAlternative), E_USER_NOTICE);
    }
    public function callBack($oCallable, $asParameters=array())
    {
        $_aParameters = self::getAsArray($asParameters, true);
        $_mDefaultValue = self::getElement($_aParameters, 0);
        return is_callable($oCallable) ? call_user_func_array($oCallable, $_aParameters) : $_mDefaultValue;
    }
    public static function hasBeenCalled($sKey)
    {
        if (isset(self::$___aCallStack[ $sKey ])) {
            return true;
        }
        self::$___aCallStack[ $sKey ] = true;
        return false;
    }
    private static $___aCallStack = array();
    public static function getOutputBuffer($cCallable, array $aParameters=array())
    {
        ob_start();
        echo call_user_func_array($cCallable, $aParameters);
        $_sContent = ob_get_contents();
        ob_end_clean();
        return $_sContent;
    }
    public static function getObjectInfo($oInstance)
    {
        $_iCount = count(get_object_vars($oInstance));
        $_sClassName = get_class($oInstance);
        return '(object) ' . $_sClassName . ': ' . $_iCount . ' properties.';
    }
    public static function getAOrB($mValue, $mTrue=null, $mFalse=null)
    {
        return $mValue ? $mTrue : $mFalse;
    }
}
