<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2014 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since 3.0.0
 * @extends AdminPageFramework_Utility_Array
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_URL extends AdminPageFramework_Utility_Path {

    /**
     * Retrieves the currently loaded page url.
     * 
     * @since 3.0.1
     */
    static public function getCurrentURL() {
        $sSSL = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? true:false;
        $sServerProtocol = strtolower( $_SERVER['SERVER_PROTOCOL'] );
        $sProtocol = substr( $sServerProtocol, 0, strpos( $sServerProtocol, '/' ) ) . ( ( $sSSL ) ? 's' : '' );
        $sPort = $_SERVER['SERVER_PORT'];
        $sPort = ( ( !$sSSL && $sPort=='80' ) || ( $sSSL && $sPort=='443' ) ) ? '' : ':' . $sPort;
        $sHost = isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        return $sProtocol . '://' . $sHost . $sPort . $_SERVER['REQUEST_URI'];
    }
    
}