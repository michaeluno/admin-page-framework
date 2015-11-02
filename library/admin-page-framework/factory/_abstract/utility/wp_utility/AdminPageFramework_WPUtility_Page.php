<?php
class AdminPageFramework_WPUtility_Page extends AdminPageFramework_WPUtility_HTML {
    static public function getCurrentPostType() {
        static $_sCurrentPostType;
        if ($_sCurrentPostType) {
            return $_sCurrentPostType;
        }
        $_sCurrentPostType = self::_getCurrentPostType();
        return $_sCurrentPostType;
    }
    static private function _getCurrentPostType() {
        $_aMethodsToTry = array('getPostTypeByTypeNow', 'getPostTypeByScreenObject', 'getPostTypeByREQUEST', 'getPostTypeByPostObject',);
        foreach ($_aMethodsToTry as $_sMethodName) {
            $_sPostType = call_user_func(array(__CLASS__, $_sMethodName));
            if ($_sPostType) {
                return $_sPostType;
            }
        }
        return null;
    }
    static public function getPostTypeByTypeNow() {
        if (isset($GLOBALS['typenow']) && $GLOBALS['typenow']) {
            return $GLOBALS['typenow'];
        }
    }
    static public function getPostTypeByScreenObject() {
        if (isset($GLOBALS['current_screen']->post_type) && $GLOBALS['current_screen']->post_type) {
            return $GLOBALS['current_screen']->post_type;
        }
    }
    static public function getPostTypeByREQUEST() {
        if (isset($_REQUEST['post_type'])) {
            return sanitize_key($_REQUEST['post_type']);
        }
        if (isset($_GET['post']) && $_GET['post']) {
            return get_post_type($_GET['post']);
        }
    }
    static public function getPostTypeByPostObject() {
        if (isset($GLOBALS['post'], $GLOBALS['post']->post_type) && $GLOBALS['post']->post_type) {
            return $GLOBALS['post']->post_type;
        }
    }
    static public function isCustomTaxonomyPage($asPostTypes = array()) {
        if (!in_array(self::getPageNow(), array('tags.php', 'edit-tags.php',))) {
            return false;
        }
        return self::isCurrentPostTypeIn($asPostTypes);
    }
    static public function isPostDefinitionPage($asPostTypes = array()) {
        if (!in_array(self::getPageNow(), array('post.php', 'post-new.php',))) {
            return false;
        }
        return self::isCurrentPostTypeIn($asPostTypes);
    }
    static public function isCurrentPostTypeIn($asPostTypes) {
        $_aPostTypes = self::getAsArray($asPostTypes);
        if (empty($_aPostTypes)) {
            return true;
        }
        return in_array(self::getCurrentPostType(), $_aPostTypes);
    }
    static public function isPostListingPage($asPostTypes = array()) {
        if ('edit.php' != self::getPageNow()) {
            return false;
        }
        $_aPostTypes = self::getAsArray($asPostTypes);
        if (!isset($_GET['post_type'])) {
            return in_array('post', $_aPostTypes);
        }
        return in_array($_GET['post_type'], $_aPostTypes);
    }
    static private $_sPageNow;
    static public function getPageNow() {
        if (isset(self::$_sPageNow)) {
            return self::$_sPageNow;
        }
        if (isset($GLOBALS['pagenow'])) {
            self::$_sPageNow = $GLOBALS['pagenow'];
            return self::$_sPageNow;
        }
        self::$_sPageNow = is_admin() ? self::_getPageNow_BackEnd() : self::_getPageNow_FrontEnd();
        return self::$_sPageNow;
    }
    static private function _getPageNow_FrontEnd() {
        if (preg_match('#([^/]+\.php)([?/].*?)?$#i', $_SERVER['PHP_SELF'], $_aMatches)) {
            return strtolower($_aMatches[1]);
        }
        return 'index.php';
    }
    static private function _getPageNow_BackEnd() {
        $_sPageNow = self::_getPageNowAdminURLBasePath();
        if (self::_isInAdminIndex($_sPageNow)) {
            return 'index.php';
        }
        preg_match('#(.*?)(/|$)#', $_sPageNow, $_aMatches);
        $_sPageNow = strtolower($_aMatches[1]);
        if ('.php' !== substr($_sPageNow, -4, 4)) {
            $_sPageNow.= '.php';
        }
        return $_sPageNow;
    }
    static private function _getPageNowAdminURLBasePath() {
        if (is_network_admin()) {
            $_sNeedle = '#/wp-admin/network/?(.*?)$#i';
        } else if (is_user_admin()) {
            $_sNeedle = '#/wp-admin/user/?(.*?)$#i';
        } else {
            $_sNeedle = '#/wp-admin/?(.*?)$#i';
        }
        preg_match($_sNeedle, $_SERVER['PHP_SELF'], $_aMatches);
        return preg_replace('#\?.*?$#', '', trim($_aMatches[1], '/'));
    }
    static private function _isInAdminIndex($sPageNow) {
        return in_array($sPageNow, array('', 'index', 'index.php'));
    }
    static public function getCurrentScreenID() {
        $_oScreen = get_current_screen();
        if (is_string($_oScreen)) {
            $_oScreen = convert_to_screen($_oScreen);
        }
        if (isset($_oScreen->id)) {
            return $_oScreen->id;
        }
        if (isset($GLBOALS['page_hook'])) {
            return is_network_admin() ? $GLBOALS['page_hook'] . '-network' : $GLBOALS['page_hook'];
        }
        return '';
    }
    static public function getNumberOfScreenColumns() {
        return get_current_screen()->get_columns();
    }
}