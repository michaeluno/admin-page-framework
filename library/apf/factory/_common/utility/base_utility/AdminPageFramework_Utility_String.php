<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_String extends AdminPageFramework_Utility_VariableType {
    public static function getAsString($mValue)
    {
        if (is_string($mValue)) {
            return $mValue;
        }
        if (is_scalar($mValue)) {
            return ( string ) $mValue;
        }
        if (empty($mValue)) {
            return '';
        }
        return ( string ) $mValue;
    }
    public static function getLengthSanitized($sLength, $sUnit='px')
    {
        $sLength = $sLength ? $sLength : 0;
        return is_numeric($sLength) ? $sLength . $sUnit : $sLength;
    }
    public static function sanitizeSlug($sSlug)
    {
        return is_null($sSlug) ? null : preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim($sSlug));
    }
    public static function sanitizeString($sString)
    {
        return is_null($sString) ? null : preg_replace('/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString);
    }
    public static function getNumberFixed($nToFix, $nDefault, $nMin='', $nMax='')
    {
        if (! is_numeric(trim($nToFix))) {
            return $nDefault;
        }
        if ($nMin !== '' && $nToFix < $nMin) {
            return $nMin;
        }
        if ($nMax !== '' && $nToFix > $nMax) {
            return $nMax;
        }
        return $nToFix;
    }
    public static function fixNumber($nToFix, $nDefault, $nMin='', $nMax='')
    {
        return self::getNumberFixed($nToFix, $nDefault, $nMin, $nMax);
    }
    public static function getCSSMinified($sCSSRules)
    {
        return str_replace(array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCSSRules));
    }
    public static function getStringLength($sString)
    {
        return function_exists('mb_strlen') ? mb_strlen($sString) : strlen($sString);
    }
    public static function getNumberOfReadableSize($nSize)
    {
        $_nReturn = substr($nSize, 0, -1);
        switch (strtoupper(substr($nSize, -1))) { case 'P': $_nReturn *= 1024;
        // no break
        case 'T': $_nReturn *= 1024;
        // no break
        case 'G': $_nReturn *= 1024;
        // no break
        case 'M': $_nReturn *= 1024;
        // no break
        case 'K': $_nReturn *= 1024; }
        return $_nReturn;
    }
    public static function getReadableBytes($nBytes, $iRoundPrecision=2)
    {
        $_aUnits = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
        $_nLog = log($nBytes, 1024);
        $_iPower = ( int ) $_nLog;
        $_ifSize = pow(1024, $_nLog - $_iPower);
        $_ifSize = round($_ifSize, $iRoundPrecision);
        return $_ifSize . $_aUnits[ $_iPower ];
    }
    public static function getPrefixRemoved($sString, $sPrefix)
    {
        return self::hasPrefix($sPrefix, $sString) ? substr($sString, strlen($sPrefix)) : $sString;
    }
    public static function getSuffixRemoved($sString, $sSuffix)
    {
        return self::hasSuffix($sSuffix, $sString) ? substr($sString, 0, strlen($sSuffix) * - 1) : $sString;
    }
    public static function hasPrefix($sNeedle, $sHaystack)
    {
        return ( string ) $sNeedle === substr($sHaystack, 0, strlen(( string ) $sNeedle));
    }
    public static function hasSuffix($sNeedle, $sHaystack)
    {
        $_iLength = strlen(( string ) $sNeedle);
        if (0 === $_iLength) {
            return true;
        }
        return substr($sHaystack, - $_iLength) === $sNeedle;
    }
    public static function hasSlash($sString)
    {
        $sString = str_replace('\\', '/', $sString);
        return (false !== strpos($sString, '/'));
    }
}
