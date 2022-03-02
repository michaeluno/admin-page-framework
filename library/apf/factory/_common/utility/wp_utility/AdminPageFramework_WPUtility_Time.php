<?php
/*
 * Admin Page Framework v3.9.1b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_Time extends AdminPageFramework_WPUtility_SystemInformation {
    public static function getSiteReadableDate($iTimeStamp, $sDateTimeFormat=null, $bAdjustGMT=false)
    {
        static $_iOffsetSeconds, $_sDateFormat, $_sTimeFormat;
        $_iOffsetSeconds = $_iOffsetSeconds ? $_iOffsetSeconds : self::getGMTOffset();
        $_sDateFormat = $_sDateFormat ? $_sDateFormat : get_option('date_format');
        $_sTimeFormat = $_sTimeFormat ? $_sTimeFormat : get_option('time_format');
        $sDateTimeFormat = $sDateTimeFormat ? $sDateTimeFormat : $_sDateFormat . ' ' . $_sTimeFormat;
        if (! $iTimeStamp) {
            return 'n/a';
        }
        $iTimeStamp = $bAdjustGMT ? $iTimeStamp + $_iOffsetSeconds : $iTimeStamp;
        return date_i18n($sDateTimeFormat, $iTimeStamp);
    }
    public static function getGMTOffsetString()
    {
        $_fGMTOffsetHours = (self::getGMTOffset() / 3600);
        return self::___getNumberedOffsetString($_fGMTOffsetHours);
    }
    public static function getGMTOffset()
    {
        $_iCache = self::getObjectCache(__METHOD__);
        if (isset($_iCache)) {
            return $_iCache;
        }
        try {
            $_sTimeZone = self::___getSiteTimeZone();
            $_oDateTimeZone = new DateTimeZone($_sTimeZone);
            $_oDateTime = new DateTime("now", $_oDateTimeZone);
        } catch (Exception $oException) {
            self::setObjectCache(__METHOD__, 0);
            return 0;
        }
        $_iOffset = $_oDateTimeZone->getOffset($_oDateTime);
        self::setObjectCache(__METHOD__, $_iOffset);
        return $_iOffset;
    }
    private static function ___getSiteTimeZone()
    {
        $_sTimeZone = get_option('timezone_string');
        if (! empty($_sTimeZone)) {
            return $_sTimeZone;
        }
        $_fOffset = get_option('gmt_offset', 0);
        return self::___getNumberedOffsetString($_fOffset);
    }
    private static function ___getNumberedOffsetString($fOffset)
    {
        $_iHours = ( integer ) $fOffset;
        $_fiMinutes = abs(($fOffset - ( integer ) $fOffset) * 60);
        return sprintf('%+03d:%02d', $_iHours, $_fiMinutes);
    }
}
