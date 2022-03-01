<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods which use WordPress functions.
 *
 * @since    3.9.0
 * @package  AdminPageFramework/Utility
 */
class AdminPageFramework_WPUtility_Time extends AdminPageFramework_WPUtility_SystemInformation {

    /**
     * Returns the readable date-time string.
     * @param integer     $iTimeStamp
     * @param null|string $sDateTimeFormat
     * @param boolean     $bAdjustGMT
     * @return string
     */
    static public function getSiteReadableDate( $iTimeStamp, $sDateTimeFormat=null, $bAdjustGMT=false ) {

        static $_iOffsetSeconds, $_sDateFormat, $_sTimeFormat;
        $_iOffsetSeconds = $_iOffsetSeconds
            ? $_iOffsetSeconds
            : self::getGMTOffset();
        $_sDateFormat = $_sDateFormat
            ? $_sDateFormat
            : get_option( 'date_format' );
        $_sTimeFormat = $_sTimeFormat
            ? $_sTimeFormat
            : get_option( 'time_format' );
        $sDateTimeFormat = $sDateTimeFormat
            ? $sDateTimeFormat
            : $_sDateFormat . ' ' . $_sTimeFormat;

        if ( ! $iTimeStamp ) {
            return 'n/a';
        }
        $iTimeStamp = $bAdjustGMT ? $iTimeStamp + $_iOffsetSeconds : $iTimeStamp;
        return date_i18n( $sDateTimeFormat, $iTimeStamp );

    }
    
    /**
     * @return string e.g. +09:00
     * @since  3.9.0
     */
    public static function getGMTOffsetString() {
        $_fGMTOffsetHours = ( self::getGMTOffset() / 3600 ); // * 100;
        return self::___getNumberedOffsetString( $_fGMTOffsetHours );
    }

    /**
     * Determine time zone from WordPress options and return as object.
     *
     * @return integer The timezone offset in seconds.
     * @see    https://wordpress.stackexchange.com/a/283094
     * @since  3.9.0
     */
    public static function getGMTOffset() {

        $_iCache = self::getObjectCache( __METHOD__ );
        if ( isset( $_iCache ) ) {
            return $_iCache;
        }
        try {
            $_sTimeZone     = self::___getSiteTimeZone();
            $_oDateTimeZone = new DateTimeZone( $_sTimeZone );
            $_oDateTime     = new DateTime("now", $_oDateTimeZone );
        } catch ( Exception $oException ) {
            self::setObjectCache( __METHOD__, 0 );
            return 0;
        }
        $_iOffset = $_oDateTimeZone->getOffset( $_oDateTime );
        self::setObjectCache( __METHOD__, $_iOffset );
        return $_iOffset;

    }
        /**
         * @return string Timezone string compatible with the DateTimeZone objects.
         * @since  3.9.0
         */
        static private function ___getSiteTimeZone() {
            $_sTimeZone = get_option( 'timezone_string' );
            if ( ! empty( $_sTimeZone ) ) {
                return $_sTimeZone;
            }
            $_fOffset   = get_option( 'gmt_offset', 0 ); // e.g. 5.5
            return self::___getNumberedOffsetString( $_fOffset );
        }

        /**
         * @param  float  $fOffset
         * @return string
         * @since  3.9.0
         */
        static private function ___getNumberedOffsetString( $fOffset ) {
            $_iHours    = ( integer ) $fOffset;
            $_fiMinutes = abs( ( $fOffset - ( integer ) $fOffset ) * 60 );
            return sprintf( '%+03d:%02d', $_iHours, $_fiMinutes );
        }

}