<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2019, Michael Uno; Licensed MIT
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
     * Retrieves the current URL in the admin page.
     *
     * @since 2.1.1
     */
    static public function getCurrentAdminURL() {

        $sRequestURI    = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"];
        $sPageURL       = 'on' == @$_SERVER["HTTPS"] ? "https://" : "http://";

        if ( "80" != $_SERVER["SERVER_PORT"] ) {
            $sPageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $sRequestURI;
        } else {
            $sPageURL .= $_SERVER["SERVER_NAME"] . $sRequestURI;
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
            : add_query_arg( $_GET, $_sAdminURL );

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
     * @since   3.7.9   Changed not to escape the returining url.
     * @static
     * @access  public
     * @return  string The source url
     */
    static public function getSRCFromPath( $sFilePath ) {

        $_oWPStyles      = new WP_Styles(); // It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
        $_sRelativePath  = AdminPageFramework_Utility::getRelativePath( preg_replace( '/[\/\\\\]+wp-content(.*)/m', '', WP_CONTENT_DIR ), $sFilePath );
        $_sRelativePath  = preg_replace( "/^\.[\/\\\]/", '', $_sRelativePath, 1 ); // removes the heading ./ or .\
        $_sHref          = trailingslashit( $_oWPStyles->base_url ) . $_sRelativePath;
        unset( $_oWPStyles ); // for PHP 5.2.x or below
        return $_sHref;

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
         */
        static public function resolveSRC( $sSRC, $bReturnNullIfNotExist=false ) {
            return self::getResolvedSRC( $sSRC, $bReturnNullIfNotExist );
        }

}
