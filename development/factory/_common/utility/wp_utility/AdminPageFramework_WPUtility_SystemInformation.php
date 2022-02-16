<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods to retrieve various system information.
 *
 * @since       3.4.6
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility_SystemInformation extends AdminPageFramework_WPUtility_SiteInformation {

    /**
     * Caches the MySQL information.
     * @since       3.4.6
     */
    static private $___aMySQLInfo;

    /**
     * Returns MySQL configurations.
     * @since  3.4.6
     * @return array
     */
    static public function getMySQLInfo() {

        if ( isset( self::$___aMySQLInfo ) ) {
            return self::$___aMySQLInfo;
        }

        global $wpdb;

        $_aOutput = array(
            'Version'   => isset( $wpdb->use_mysqli ) && $wpdb->use_mysqli
                ? @mysqli_get_server_info( $wpdb->dbh )
                : @mysql_get_server_info(),
        );

        foreach( ( array ) $wpdb->get_results( "SHOW VARIABLES", ARRAY_A ) as $_iIndex => $_aItem ) {
            $_aItem     = array_values( $_aItem );
            $_sKey      = array_shift( $_aItem );
            $_sValue    = array_shift( $_aItem );
            $_aOutput[ $_sKey ] = $_sValue;
        }

        self::$___aMySQLInfo = $_aOutput;
        return self::$___aMySQLInfo;

    }

    /**
     * Returns the MySQL error log path.
     * @since  3.4.6
     * @return string
     */
    static public function getMySQLErrorLogPath() {
        $_aMySQLInfo = self::getMySQLInfo();
        return isset( $_aMySQLInfo[ 'log_error' ] )
            ? $_aMySQLInfo[ 'log_error' ]
            : '';
    }

    /**
     * Returns a PHP error log.
     * @since  3.4.6
     * @return string
     */
    static public function getMySQLErrorLog( $iLines=1 ) {
        $_sLog = self::getFileTailContents( self::getMySQLErrorLogPath(), $iLines );
        return $_sLog ? $_sLog : '';    // @todo If empty, return an alternative.
    }

}