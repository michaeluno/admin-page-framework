<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which use WordPress functions.
 *
 * @since           2.0.0
 * @extends         AdminPageFramework_WPUtility_SystemInformation
 * @package         AdminPageFramework
 * @subpackage      Utility
 * @internal
 */
class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_SystemInformation {
    
    /**
     * Retrieves the `wp-admin` directory path without a trailing slash.
     * 
     * @see         http://www.andrezrv.com/2014/11/11/correctly-obtain-path-admin-directory/
     * @since       DEVVER
     * @return      string
     */
    static public function getWPAdminDirPath() {
        
        $_aFunctionNames = array(
            0 => 'get_admin_url',
            1 => 'get_network_admin_url'
        );
        $_sWPAdminPath = str_replace( 
            get_bloginfo( 'url' ) . '/', 
            ABSPATH, 
            call_user_func( $_aFunctionNames[ ( integer ) is_network_admin() ] )
        );
        return rtrim( $_sWPAdminPath, '/' );
        
    }
    
    /**
     * Redirects the page viewer to the specified local url.
     * 
     * Use this method to redirect the viewer within the operating site such as form submission and information page.
     * 
     * @uses        wp_safe_redirect
     * @since       DEVVER
     * @return      void
     */
    static public function goToLocalURL( $sURL, $oCallbackOnError=null ) {
        self::redirectByType( $sURL, 1, $oCallbackOnError );
    }
    
    /**
     * Redirects the page viewer to the specified url.
     * @uses        wp_redirect
     * @since       3.6.3
     * @since       DEVVER      Added the second callback parameter.
     * @return      void
     */
    static public function goToURL( $sURL, $oCallbackOnError=null ) {
        self::redirectByType( $sURL, 0, $oCallbackOnError );
    }
    
    /**
     * Performs a redirect and exits the script.
     * @param       string      $sURL               The url to get redirected.
     * @param       integer     $iType              0: external site, 1: local site (within the same domain).
     * @param       callable    $oCallbackOnError
     */
    static public function redirectByType( $sURL, $iType=0, $oCallbackOnError=null ) {
     
        $_iRedirectError = self::getRedirectPreError( $sURL, $iType );
        if ( $_iRedirectError && is_callable( $oCallbackOnError ) ) {
            call_user_func_array(
                $oCallbackOnError,
                array( 
                    $_iRedirectError,
                    $sURL,
                )
            );
            return; // do not redirect
        }
        $_sFunctionName = array(
            0 => 'wp_redirect',
            1 => 'wp_safe_redirect',
        );
        exit( $_sFunctionName[ ( integer ) $iType ]( $sURL ) );
        
    }

    /**
     * Checks whether a redirect can proceed.
     * @since       DEVVER
     * @param       string      $sURL               The url to get redirected.
     * @param       integer     $iType              0: external site, 1: local site (within the same domain).
     * @return      integer     0: no problem, 1: url is no valid, 2: HTTP headers already sent.
     */
    static public function getRedirectPreError( $sURL, $iType ) {
        
        // check only externnal urls as local ones can be a relative url and always fails the below check.
        if ( ! $iType && filter_var( $sURL, FILTER_VALIDATE_URL) === false ) {
            return 1;
        }
        // If HTTP headers are already sent, redirect cannot be done.
        if ( headers_sent() ) {
            return 2;
        }
        return 0;
    }
    
    /**
     * Checks whether the site is in the debug mode or not.
     * @since       3.5.7
     * @return      boolean     
     */
    static public function isDebugMode() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }
    
    /**
     * Checks whether the page is loaded as an Ajax request.
     * @since       3.5.7
     * @return      boolean
     */
    static public function isDoingAjax() {
        return defined( 'DOING_AJAX' ) && DOING_AJAX;
    }
        
    /**
     * Flushes the site rewrite rules.
     *
     * The method ensures it is done no more than once in a page load.
     * 
     * @since       3.1.5
     */
    static public function flushRewriteRules() {
        
        if ( self::$_bIsFlushed ) {
            return;
        }
        flush_rewrite_rules();
        self::$_bIsFlushed = true;
        
    }    
        /**
         * Indicates whether the flushing rewrite rules has been performed or not.
         * @since       3.1.5
         */
        static private $_bIsFlushed = false;
        
}