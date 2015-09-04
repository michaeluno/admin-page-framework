<?php
abstract class AdminPageFramework_Utility_String extends AdminPageFramework_Utility_Deprecated {
    public static function sanitizeSlug($sSlug) {
        return is_null($sSlug) ? null : preg_replace('/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim($sSlug));
    }
    public static function sanitizeString($sString) {
        return is_null($sString) ? null : preg_replace('/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $sString);
    }
    static public function fixNumber($nToFix, $nDefault, $nMin = '', $nMax = '') {
        if (!is_numeric(trim($nToFix))) {
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
    static public function minifyCSS($sCSSRules) {
        return str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sCSSRules));
    }
    static public function getStringLength($sString) {
        return function_exists('mb_strlen') ? mb_strlen($sString) : strlen($sString);
    }
    static public function getNumberOfReadableSize($nSize) {
        $_nReturn = substr($nSize, 0, -1);
        switch (strtoupper(substr($nSize, -1))) {
            case 'P':
                $_nReturn*= 1024;
            case 'T':
                $_nReturn*= 1024;
            case 'G':
                $_nReturn*= 1024;
            case 'M':
                $_nReturn*= 1024;
            case 'K':
                $_nReturn*= 1024;
        }
        return $_nReturn;
    }
    static public function getReadableBytes($nBytes) {
        $_aUnits = array(0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB');
        $_nLog = log($nBytes, 1024);
        $_iPower = ( int )$_nLog;
        $_iSize = pow(1024, $_nLog - $_iPower);
        return $_iSize . $_aUnits[$_iPower];
    }
    static public function hasPrefix($sNeedle, $sHaystack) {
        return $sNeedle === substr($sHaystack, 0, strlen($sNeedle));
    }
    static public function hasSuffix($sNeedle, $sHaystack) {
        $_iLength = strlen($sNeedle);
        if (0 === $_iLength) {
            return true;
        }
        return substr($sHaystack, -$_iLength) === $sNeedle;
    }
}