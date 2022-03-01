<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_SystemInformation extends AdminPageFramework_WPUtility_SiteInformation {
    private static $___aMySQLInfo;
    public static function getMySQLInfo()
    {
        if (isset(self::$___aMySQLInfo)) {
            return self::$___aMySQLInfo;
        }
        global $wpdb;
        $_aOutput = array( 'Version' => isset($wpdb->use_mysqli) && $wpdb->use_mysqli ? @mysqli_get_server_info($wpdb->dbh) : @mysql_get_server_info(), );
        foreach (( array ) $wpdb->get_results("SHOW VARIABLES", ARRAY_A) as $_iIndex => $_aItem) {
            $_aItem = array_values($_aItem);
            $_sKey = array_shift($_aItem);
            $_sValue = array_shift($_aItem);
            $_aOutput[ $_sKey ] = $_sValue;
        }
        self::$___aMySQLInfo = $_aOutput;
        return self::$___aMySQLInfo;
    }
    public static function getMySQLErrorLogPath()
    {
        $_aMySQLInfo = self::getMySQLInfo();
        return isset($_aMySQLInfo[ 'log_error' ]) ? $_aMySQLInfo[ 'log_error' ] : '';
    }
    public static function getMySQLErrorLog($iLines=1)
    {
        $_sLog = self::getFileTailContents(self::getMySQLErrorLogPath(), $iLines);
        return $_sLog ? $_sLog : '';
    }
}
