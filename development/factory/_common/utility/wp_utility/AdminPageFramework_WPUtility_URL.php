<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2022, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods handing urls which use WordPress functions and classes.
 *
 * @since       2.0.0
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility_URL extends AdminPageFramework_Utility {

    /**
     * @var   array Caches the HTTP GET query values.
     * @since 3.8.32
     */
    static private $___aGET;

    /**
     * Returns sanitized values of GET HTTP queries.
     * @param  array|string $asKeys Dimensional keys for the value to retrieve. If empty, the entire array will be returned.
     * @return string|array
     * @since  3.8.32
     */
    static public function getHTTPQueryGET( $asKeys=array(), $mDefault=null ) {
        self::$___aGET = isset( self::$___aGET )
            ? self::$___aGET
            : self::getArrayMappedRecursive( 'sanitize_text_field', $_GET );    // sanitization done
        if ( empty( $asKeys ) ) {
            return self::$___aGET;
        }
        return self::getElement( self::$___aGET, $asKeys, $mDefault );
    }

    /**
     * Retrieves the current URL in the admin page.
     *
     * @since  2.1.1
     * @return string
     */
    static public function getCurrentAdminURL() {

        $sRequestURI    = $GLOBALS[ 'is_IIS' ] ? $_SERVER[ 'PATH_INFO' ] : $_SERVER[ "REQUEST_URI" ];
        $sPageURL       = 'on' == @$_SERVER[ "HTTPS" ] ? "https://" : "http://";

        if ( "80" != $_SERVER[ "SERVER_PORT" ] ) {
            $sPageURL .= $_SERVER[ "SERVER_NAME" ] . ":" . $_SERVER[ "SERVER_PORT" ] . $sRequestURI;
        } else {
            $sPageURL .= $_SERVER[ "SERVER_NAME" ] . $sRequestURI;
        }
        return $sPageURL;

    }

    /**
     * Returns a url with modified query stings.
     *
     * Identical to the getQueryURL() method except that if the third parameter is omitted, it will use the currently browsed admin url.
     *
     * @since 2.1.2
     * @param array $aAddingQueries The appending query key value pairs e.g. array( 'page' => 'my_page_slug', 'tab' => 'my_tab_slug' )
     * @param array $aRemovingQueryKeys ( optional ) The removing query keys. e.g. array( 'settings-updated', 'my-custom-admin-notice' )
     * @param string $sSubjectURL ( optional ) The subject url to modify
     * @return string The modified url.
     */
    static public function getQueryAdminURL( $aAddingQueries=array(), $aRemovingQueryKeys=array(), $sSubjectURL='' ) {

        $_sAdminURL = is_network_admin()
            ? network_admin_url( AdminPageFramework_WPUtility_Page::getPageNow() )
            : admin_url( AdminPageFramework_WPUtility_Page::getPageNow() );

        $sSubjectURL = $sSubjectURL
            ? $sSubjectURL
            : add_query_arg( self::getHTTPQueryGET(), $_sAdminURL );

        return self::getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL );

    }
    /**
     * Returns a url with modified query stings.
     *
     * @since 2.1.2
     * @param array $aAddingQueries The appending query key value pairs
     * @param array $aRemovingQueryKeys The removing query key value pairs
     * @param string $sSubjectURL The subject url to modify
     * @return string The modified url.
     */
    static public function getQueryURL( $aAddingQueries, $aRemovingQueryKeys, $sSubjectURL ) {

        // Remove Queries
        $sSubjectURL = empty( $aRemovingQueryKeys )
            ? $sSubjectURL
            : remove_query_arg( ( array ) $aRemovingQueryKeys, $sSubjectURL );

        // Add Queries
        $sSubjectURL = add_query_arg( $aAddingQueries, $sSubjectURL );

        return $sSubjectURL;

    }

    /**
     * Calculates the URL from the given path.
     *
     * @since   2.1.5
     * @since   3.7.9  Changed not to escape the returining url.
     * @return  string The source url
     * @param   string $sFilePath
     * @remark  The parsable path is limited to under the WP_CONTENT_DIR directory.
     */
    static public function getSRCFromPath( $sFilePath ) {
        $sFilePath        = str_replace('\\', '/', $sFilePath );
        $_sContentDirPath = str_replace('\\', '/', WP_CONTENT_DIR );
        if ( false !== strpos( $sFilePath, $_sContentDirPath ) ) {
            $_sRelativePath = AdminPageFramework_Utility::getRelativePath( WP_CONTENT_DIR , $sFilePath );
            $_sRelativePath = preg_replace("/^\.[\/\\\]/", '', $_sRelativePath, 1 );
            return content_url( $_sRelativePath );
        }
        $_sRelativePath = AdminPageFramework_Utility::getRelativePath( ABSPATH , $sFilePath );
        $_sRelativePath = preg_replace("/^\.[\/\\\]/", '', $_sRelativePath, 1 );
        return trailingslashit( get_bloginfo( 'url' ) ) . $_sRelativePath;
    }

    /**
     * Resolves the given src and returns the url.
     *
     * Checks if the given string is a url, a relative path, or an absolute path and returns the url if it's not a relative path.
     *
     * @since       2.1.5
     * @since       2.1.6       Moved from the AdminPageFramework_Resource_Base class. Added the `$bReturnNullIfNotExist` parameter.
     * @since       3.6.0       Changed the name from `resolveSRC()`.
     * @since       3.7.9       Changed not to escape characters.
     * @return      string|null
     * @param       string      $sSRC
     * @param       boolean     $bReturnNullIfNotExist
     */
    static public function getResolvedSRC( $sSRC, $bReturnNullIfNotExist=false ) {

        if ( ! self::isResourcePath( $sSRC ) ) {
            return $bReturnNullIfNotExist
                ? null
                : $sSRC;
        }

        // It is a url
        if ( filter_var( $sSRC, FILTER_VALIDATE_URL ) ) {
            return $sSRC;
        }

        // If the file exists, it means it is an absolute path. If so, calculate the URL from the path.
        if ( file_exists( realpath( $sSRC ) ) ) {
            return self::getSRCFromPath( $sSRC );   // url escaping is done in the method
        }

        if ( $bReturnNullIfNotExist ) {
            return null;
        }

        // Otherwise, assume the string is a relative path 'to the WordPress installed absolute path'.
        return $sSRC;

    }
        /**
         * @deprecated      3.6.0       Use `getResolvedSRC()` instead.
         * @param string $sSRC
         * @param boolean $bReturnNullIfNotExist
         * @return string
         */
        static public function resolveSRC( $sSRC, $bReturnNullIfNotExist=false ) {
            return self::getResolvedSRC( $sSRC, $bReturnNullIfNotExist );
        }

}
