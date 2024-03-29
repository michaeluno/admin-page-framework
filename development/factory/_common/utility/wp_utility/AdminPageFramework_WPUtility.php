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
 * @since    2.0.0
 * @package  AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_Time {

    /**
     * @var integer
     * @sinec 3.8.32
     */
    static private $___iCustomNonceLifeSpan;

    /**
     * @param  integer|string $sAction   The action name to pass to wp_create_nonce()
     * @param  integer        $iLifespan Defualts to 86400 which is equal to `DAY_IN_SECONDS`.
     * @return string
     * @see    wp_create_nonce()
     * @since  3.8.32
     */
    static public function getNonceCreated( $sAction=-1, $iLifespan=86400 ) {
        self::$___iCustomNonceLifeSpan = $iLifespan;
        add_filter( 'nonce_life', array( __CLASS__, '_replyToSetNonceLifeSpan' ) );
        $_sNonce = ( string ) wp_create_nonce($sAction );
        remove_filter( 'nonce_life', array( __CLASS__, '_replyToSetNonceLifeSpan' ) );
        self::$___iCustomNonceLifeSpan = null;
        return $_sNonce ;
    }
        /**
         * @param  integer $iLifespanInSeconds
         * @return integer
         * @since  3.8.32
         */
        static public function _replyToSetNonceLifeSpan( $iLifespanInSeconds ) {
            return self::$___iCustomNonceLifeSpan;
        }

    /**
     * @param       string $sPostTypeSlug
     * @param       array $aPostTypeArguments
     * @return      string  the 'Manage' sub-menu link slug.
     * @since       3.7.4
     * @remark      When the user sets a custom slug to the `show_in_menu` argument, use it.
     */
    static public function getPostTypeSubMenuSlug( $sPostTypeSlug, $aPostTypeArguments ) {
        $_sCustomMenuSlug = self::getShowInMenuPostTypeArgument( $aPostTypeArguments );
        if ( is_string( $_sCustomMenuSlug ) ) {
            return $_sCustomMenuSlug;
        }
        return 'edit.php?post_type=' . $sPostTypeSlug;
    }

    /**
     * Returns the value of the `show_in_menu` argument from a custom post type arguments.
     * @since       3.7.4
     * @param       array           $aPostTypeArguments
     * @return      boolean|string
     */
    static public function getShowInMenuPostTypeArgument( $aPostTypeArguments ) {
        return self::getElement(
            $aPostTypeArguments, // subject array
            'show_in_menu', // dimensional keys
            self::getElement(
                $aPostTypeArguments, // subject array
                'show_ui', // dimensional keys
                self::getElement(
                    $aPostTypeArguments, // subject array
                    'public', // dimensional keys
                    false // default
                )
            )
        );
    }

    /**
     * Retrieves the `wp-admin` directory path without a trailing slash.
     *
     * @since       3.7.0
     * @return      string
     */
    static public function getWPAdminDirPath() {

        $_sWPAdminPath = str_replace(
            get_bloginfo( 'url' ) . '/', // search
            ABSPATH,    // replace
            get_admin_url() // subject - works even in the network admin
        );
        return rtrim( $_sWPAdminPath, '/' );

    }

    /**
     * Redirects the page viewer to the specified local url.
     *
     * Use this method to redirect the viewer within the operating site such as form submission and information page.
     *
     * @param       string $sURL
     * @param       null|callable $oCallbackOnError
     * @uses        wp_safe_redirect
     * @since       3.7.0
     * @return      void
     */
    static public function goToLocalURL( $sURL, $oCallbackOnError=null ) {
        self::redirectByType( $sURL, 1, $oCallbackOnError );
    }

    /**
     * Redirects the page viewer to the specified url.
     * @param       string $sURL
     * @param       null|callable $oCallbackOnError
     * @return      void
     * @uses        wp_redirect
     * @since       3.6.3
     * @since       3.7.0      Added the second callback parameter.
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
     * @since       3.7.0
     * @param       string      $sURL               The url to get redirected.
     * @param       integer     $iType              0: external site, 1: local site (within the same domain).
     * @return      integer     0: no problem, 1: url is no valid, 2: HTTP headers already sent.
     */
    static public function getRedirectPreError( $sURL, $iType ) {

        // check only external urls as local ones can be a relative url and always fails the below check.
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
        return ( boolean ) defined( 'WP_DEBUG' ) && WP_DEBUG;
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