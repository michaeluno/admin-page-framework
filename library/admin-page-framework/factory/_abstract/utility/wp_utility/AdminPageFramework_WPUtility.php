<?php
class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_SystemInformation {
    static public function goToLocalURL($sURL) {
        exit(wp_safe_redirect($sURL));
    }
    static public function goToURL($sURL) {
        exit(wp_redirect($sURL));
    }
    static public function isDebugMode() {
        return defined('WP_DEBUG') && WP_DEBUG;
    }
    static public function isDoingAjax() {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
    static public function flushRewriteRules() {
        if ($_bIsFlushed) {
            return;
        }
        flush_rewrite_rules();
        self::$_bIsFlushed = true;
    }
    static private $_bIsFlushed = false;
}