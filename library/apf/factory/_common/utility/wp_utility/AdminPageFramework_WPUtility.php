<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_Time {
    private static $___iCustomNonceLifeSpan;
    public static function getNonceCreated($sAction=-1, $iLifespan=86400)
    {
        self::$___iCustomNonceLifeSpan = $iLifespan;
        add_filter('nonce_life', array( __CLASS__, '_replyToSetNonceLifeSpan' ));
        $_sNonce = ( string ) wp_create_nonce($sAction);
        remove_filter('nonce_life', array( __CLASS__, '_replyToSetNonceLifeSpan' ));
        self::$___iCustomNonceLifeSpan = null;
        return $_sNonce ;
    }
    public static function _replyToSetNonceLifeSpan($iLifespanInSeconds)
    {
        return self::$___iCustomNonceLifeSpan;
    }
    public static function getPostTypeSubMenuSlug($sPostTypeSlug, $aPostTypeArguments)
    {
        $_sCustomMenuSlug = self::getShowInMenuPostTypeArgument($aPostTypeArguments);
        if (is_string($_sCustomMenuSlug)) {
            return $_sCustomMenuSlug;
        }
        return 'edit.php?post_type=' . $sPostTypeSlug;
    }
    public static function getShowInMenuPostTypeArgument($aPostTypeArguments)
    {
        return self::getElement($aPostTypeArguments, 'show_in_menu', self::getElement($aPostTypeArguments, 'show_ui', self::getElement($aPostTypeArguments, 'public', false)));
    }
    public static function getWPAdminDirPath()
    {
        $_sWPAdminPath = str_replace(get_bloginfo('url') . '/', ABSPATH, get_admin_url());
        return rtrim($_sWPAdminPath, '/');
    }
    public static function goToLocalURL($sURL, $oCallbackOnError=null)
    {
        self::redirectByType($sURL, 1, $oCallbackOnError);
    }
    public static function goToURL($sURL, $oCallbackOnError=null)
    {
        self::redirectByType($sURL, 0, $oCallbackOnError);
    }
    public static function redirectByType($sURL, $iType=0, $oCallbackOnError=null)
    {
        $_iRedirectError = self::getRedirectPreError($sURL, $iType);
        if ($_iRedirectError && is_callable($oCallbackOnError)) {
            call_user_func_array($oCallbackOnError, array( $_iRedirectError, $sURL, ));
            return;
        }
        $_sFunctionName = array( 0 => 'wp_redirect', 1 => 'wp_safe_redirect', );
        exit($_sFunctionName[ ( integer ) $iType ]($sURL));
    }
    public static function getRedirectPreError($sURL, $iType)
    {
        if (! $iType && filter_var($sURL, FILTER_VALIDATE_URL) === false) {
            return 1;
        }
        if (headers_sent()) {
            return 2;
        }
        return 0;
    }
    public static function isDebugMode()
    {
        return ( boolean ) defined('WP_DEBUG') && WP_DEBUG;
    }
    public static function isDoingAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
    public static function flushRewriteRules()
    {
        if (self::$_bIsFlushed) {
            return;
        }
        flush_rewrite_rules();
        self::$_bIsFlushed = true;
    }
    private static $_bIsFlushed = false;
}
