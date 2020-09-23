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
 * @since       3.0.0
 * @extends     AdminPageFramework_Utility_Path
 * @package     AdminPageFramework/Utility
 * @internal
 */
abstract class AdminPageFramework_Utility_URL extends AdminPageFramework_Utility_Path {

    /**
     * Retrieves the query value from the given URL with a key.
     *
     * @since       2.0.0
     * @since       3.5.7       Moved from `AdminPageFramework_Utility`.
     * @return      string|null
     */
    static public function getQueryValueInURLByKey( $sURL, $sQueryKey ) {

        $_aURL = parse_url( $sURL ) + array( 'query' => '' );
        parse_str( $_aURL[ 'query' ], $aQuery );
        return self::getElement(
            $aQuery, // subject array
            $sQueryKey, // key
            null // default
        );

    }

    /**
     * Retrieves the currently loaded page url.
     *
     * @since       3.0.1
     * @return      string
     */
    static public function getCurrentURL() {

        $_bSSL              = self::isSSL();

        // Protocol: HTTPS or HTTP
        $_sServerProtocol   = strtolower( $_SERVER[ 'SERVER_PROTOCOL' ] );
        $_aProtocolSuffix   = array(
            0 => '',
            1 => 's',
        );
        $_sProtocol         = substr( $_sServerProtocol, 0, strpos( $_sServerProtocol, '/' ) )
            . $_aProtocolSuffix[ ( int ) $_bSSL ];

        // Port: e.g. :80
        $_sPort             = self::_getURLPortSuffix( $_bSSL );

        // Host
        $_sHost             = isset( $_SERVER[ 'HTTP_X_FORWARDED_HOST' ] )
            ? $_SERVER[ 'HTTP_X_FORWARDED_HOST' ]
            : ( isset( $_SERVER[ 'HTTP_HOST' ] )
                ? $_SERVER[ 'HTTP_HOST' ]
                : $_SERVER[ 'SERVER_NAME' ]
            );
        $_sHost             = preg_replace( '/:.+/', '', $_sHost ); // remove the port part in case it is added.
        return $_sProtocol . '://' . $_sHost . $_sPort . $_SERVER[ 'REQUEST_URI' ];

    }
        /**
         * Returns the port suffix in the currently loading url.
         * @since       3.5.7
         * @return      string
         * @param       boolean $bSSL
         */
        static private function _getURLPortSuffix( $bSSL ) {
            $_sPort     = isset( $_SERVER[ 'SERVER_PORT' ] )
                ? ( string ) $_SERVER[ 'SERVER_PORT' ]
                : '';
            $_aPort     = array(
                0 => ':' . $_sPort,
                1 => '',
            );
            $_bPortSet  = ( ! $bSSL && '80' === $_sPort ) || ( $bSSL && '443' === $_sPort );
            return $_aPort[ ( int ) $_bPortSet ];
        }

    /**
     * Returns if the site is accessed via SSL or not.
     * @since       3.5.7
     * @return      boolean
     */
    static public function isSSL() {
        return array_key_exists( 'HTTPS', $_SERVER ) && 'on' === $_SERVER[ 'HTTPS' ];
    }

}
