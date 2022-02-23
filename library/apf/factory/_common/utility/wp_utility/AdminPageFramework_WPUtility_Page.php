<?php
/*
 * Admin Page Framework v3.9.0b15 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML
{
    public static function getCurrentPostType()
    {
        if (isset(self::$_sCurrentPostType)) {
            return self::$_sCurrentPostType;
        }
        self::$_sCurrentPostType = self::_getCurrentPostType();
        return self::$_sCurrentPostType;
    }
    private static $_sCurrentPostType;
    private static function _getCurrentPostType()
    {
        $_aMethodsToTry = array( 'getPostTypeByTypeNow', 'getPostTypeByScreenObject', 'getPostTypeByREQUEST', 'getPostTypeByPostObject', );
        foreach ($_aMethodsToTry as $_sMethodName) {
            $_sPostType = call_user_func(array( __CLASS__, $_sMethodName ));
            if ($_sPostType) {
                return $_sPostType;
            }
        }
        return null;
    }
    public static function getPostTypeByTypeNow()
    {
        if (isset($GLOBALS[ 'typenow' ]) && $GLOBALS[ 'typenow' ]) {
            return $GLOBALS[ 'typenow' ];
        }
    }
    public static function getPostTypeByScreenObject()
    {
        if (isset($GLOBALS[ 'current_screen' ]->post_type) && $GLOBALS[ 'current_screen' ]->post_type) {
            return $GLOBALS[ 'current_screen' ]->post_type;
        }
    }
    public static function getPostTypeByREQUEST()
    {
        if (isset($_REQUEST[ 'post_type' ])) {
            return sanitize_key(sanitize_text_field($_REQUEST[ 'post_type' ]));
        }
        if (isset($_GET[ 'post' ]) && $_GET[ 'post' ]) {
            return get_post_type(absint(self::getHTTPQueryGET('post', 0)));
        }
    }
    public static function getPostTypeByPostObject()
    {
        if (isset($GLOBALS[ 'post' ]->post_type) && $GLOBALS[ 'post' ]->post_type) {
            return $GLOBALS[ 'post' ]->post_type;
        }
    }
    public static function isCustomTaxonomyPage($asPostTypes=array())
    {
        if (! in_array(self::getPageNow(), array( 'tags.php', 'edit-tags.php', 'term.php' ))) {
            return false;
        }
        return self::isCurrentPostTypeIn($asPostTypes);
    }
    public static function isPostDefinitionPage($asPostTypes=array())
    {
        if (! in_array(self::getPageNow(), array( 'post.php', 'post-new.php', ))) {
            return false;
        }
        return self::isCurrentPostTypeIn($asPostTypes);
    }
    public static function isCurrentPostTypeIn($asPostTypes)
    {
        $_aPostTypes = self::getAsArray($asPostTypes);
        if (empty($_aPostTypes)) {
            return true;
        }
        return in_array(self::getCurrentPostType(), $_aPostTypes);
    }
    public static function isPostListingPage($asPostTypes=array())
    {
        if ('edit.php' != self::getPageNow()) {
            return false;
        }
        $_aPostTypes = self::getAsArray($asPostTypes);
        if (! isset($_GET[ 'post_type' ])) {
            return in_array('post', $_aPostTypes, true);
        }
        return in_array($_GET[ 'post_type' ], $_aPostTypes, true);
    }
    private static $_sPageNow;
    public static function getPageNow()
    {
        if (isset(self::$_sPageNow)) {
            return self::$_sPageNow;
        }
        if (isset($GLOBALS[ 'pagenow' ])) {
            self::$_sPageNow = $GLOBALS[ 'pagenow' ];
            return self::$_sPageNow;
        }
        $_aMethodNames = array( 0 => '_getPageNow_FrontEnd', 1 => '_getPageNow_BackEnd', );
        $_sMethodName = $_aMethodNames[ ( int ) is_admin() ];
        self::$_sPageNow = self::$_sMethodName();
        return self::$_sPageNow;
    }
    private static function _getPageNow_FrontEnd()
    {
        if (preg_match('#([^/]+\.php)([?/].*?)?$#i', $_SERVER[ 'PHP_SELF' ], $_aMatches)) {
            return strtolower($_aMatches[ 1 ]);
        }
        return 'index.php';
    }
    private static function _getPageNow_BackEnd()
    {
        $_sPageNow = self::_getPageNowAdminURLBasePath();
        if (self::_isInAdminIndex($_sPageNow)) {
            return 'index.php';
        }
        preg_match('#(.*?)(/|$)#', $_sPageNow, $_aMatches);
        $_sPageNow = strtolower($_aMatches[ 1 ]);
        if ('.php' !== substr($_sPageNow, -4, 4)) {
            $_sPageNow .= '.php';
        }
        return $_sPageNow;
    }
    private static function _getPageNowAdminURLBasePath()
    {
        if (is_network_admin()) {
            $_sNeedle = '#/wp-admin/network/?(.*?)$#i';
        } elseif (is_user_admin()) {
            $_sNeedle = '#/wp-admin/user/?(.*?)$#i';
        } else {
            $_sNeedle = '#/wp-admin/?(.*?)$#i';
        }
        preg_match($_sNeedle, $_SERVER[ 'PHP_SELF' ], $_aMatches);
        return preg_replace('#\?.*?$#', '', trim($_aMatches[ 1 ], '/'));
    }
    private static function _isInAdminIndex($sPageNow)
    {
        return in_array($sPageNow, array( '', 'index', 'index.php' ));
    }
    public static function getCurrentScreenID()
    {
        $_oScreen = get_current_screen();
        if (is_string($_oScreen)) {
            $_oScreen = convert_to_screen($_oScreen);
        }
        if (isset($_oScreen->id)) {
            return $_oScreen->id;
        }
        if (isset($GLBOALS[ 'page_hook' ])) {
            return is_network_admin() ? $GLBOALS[ 'page_hook' ] . '-network' : $GLBOALS[ 'page_hook' ];
        }
        return '';
    }
    public static function doesMetaBoxExist($sContext='')
    {
        $_aDimensions = array( 'wp_meta_boxes', $GLOBALS[ 'page_hook' ] );
        if ($sContext) {
            $_aDimensions[] = $sContext;
        }
        $_aSideMetaBoxes = self::getElementAsArray($GLOBALS, $_aDimensions);
        return count($_aSideMetaBoxes) > 0;
    }
    public static function getNumberOfScreenColumns()
    {
        return get_current_screen()->get_columns();
    }
}
