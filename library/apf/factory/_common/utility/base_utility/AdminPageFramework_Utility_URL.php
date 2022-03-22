<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AdminPageFramework_Utility_URL extends AdminPageFramework_Utility_Path {
    public static function isURL($sString)
    {
        return false !== filter_var($sString, FILTER_VALIDATE_URL);
    }
    public static function getQueryValueInURLByKey($sURL, $sQueryKey)
    {
        $_aURL = parse_url($sURL) + array( 'query' => '' );
        parse_str($_aURL[ 'query' ], $aQuery);
        return self::getElement($aQuery, $sQueryKey, null);
    }
    public static function getCurrentURL()
    {
        $_bSSL = self::isSSL();
        $_sServerProtocol = strtolower($_SERVER[ 'SERVER_PROTOCOL' ]);
        $_aProtocolSuffix = array( 0 => '', 1 => 's', );
        $_sProtocol = substr($_sServerProtocol, 0, strpos($_sServerProtocol, '/')) . $_aProtocolSuffix[ ( int ) $_bSSL ];
        $_sPort = self::_getURLPortSuffix($_bSSL);
        $_sHost = isset($_SERVER[ 'HTTP_X_FORWARDED_HOST' ]) ? $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] : (isset($_SERVER[ 'HTTP_HOST' ]) ? $_SERVER[ 'HTTP_HOST' ] : $_SERVER[ 'SERVER_NAME' ]);
        $_sHost = preg_replace('/:.+/', '', $_sHost);
        return $_sProtocol . '://' . $_sHost . $_sPort . $_SERVER[ 'REQUEST_URI' ];
    }
    private static function _getURLPortSuffix($bSSL)
    {
        $_sPort = isset($_SERVER[ 'SERVER_PORT' ]) ? ( string ) $_SERVER[ 'SERVER_PORT' ] : '';
        $_aPort = array( 0 => ':' . $_sPort, 1 => '', );
        $_bPortSet = (! $bSSL && '80' === $_sPort) || ($bSSL && '443' === $_sPort);
        return $_aPort[ ( int ) $_bPortSet ];
    }
    public static function isSSL()
    {
        return array_key_exists('HTTPS', $_SERVER) && 'on' === $_SERVER[ 'HTTPS' ];
    }
}
