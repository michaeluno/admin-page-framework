<?php
class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_SystemInformation {
    static public function isDebugMode() {
        return defined('WP_DEBUG') && WP_DEBUG;
    }
    static public function isDoingAjax() {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
    static private $_bIsFlushed;
    static public function FlushRewriteRules() {
        $_bIsFlushed = isset(self::$_bIsFlushed) ? self::$_bIsFlushed : false;
        if ($_bIsFlushed) {
            return;
        }
        flush_rewrite_rules();
        self::$_bIsFlushed = true;
    }
}