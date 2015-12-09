<?php
class AdminPageFramework_WPUtility_SiteInformation extends AdminPageFramework_WPUtility_Meta {
    static public function isDebugModeEnabled() {
        return ( bool )defined('WP_DEBUG') && WP_DEBUG;
    }
    static public function isDebugLogEnabled() {
        return ( bool )defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
    }
    static public function isDebugDisplayEnabled() {
        return ( bool )defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;
    }
    static public function getSiteLanguage($sDefault = 'en_US') {
        return defined('WPLANG') && WPLANG ? WPLANG : $sDefault;
    }
}