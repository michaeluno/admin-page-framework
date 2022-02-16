<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2021, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides utility methods to retrieve various system information.
 *
 * @since       3.5.3
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework/Utility
 * @internal
 */
class AdminPageFramework_WPUtility_SiteInformation extends AdminPageFramework_WPUtility_Meta {

    /**
     * Retrieves the site debug data.
     *
     * Same as the one displayed in the Site Health screen (Dashboard -> Tools -> Site Health -> Info).
     *
     * @return array    If the used WordPress version does not support the debug class, an empty array is returned.
     * @since  3.9.0
     */
    static public function getSiteData() {
        $_sWPDebugClassFilePath = ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
        if ( file_exists( $_sWPDebugClassFilePath ) ) {
            include_once( $_sWPDebugClassFilePath );
        }
        if ( ! class_exists( 'WP_Debug_Data' ) ) {
            return array();
        }
        try {
            return WP_Debug_Data::debug_data();
        } catch ( Exception $_oException ) {
            return array();
        }
    }

    /**
     * Checks if the site debug mode is on.
     *
     * @since       3.5.3
     * @return      boolean
     * @deprecated  Use `isDebugMode()` instead.
     */
    static public function isDebugModeEnabled() {
        return ( bool ) defined( 'WP_DEBUG' ) && WP_DEBUG;
    }

    /**
     * Checks if the site enables debug logs.
     * @since       3.5.3
     * @return      boolean
     */
    static public function isDebugLogEnabled() {
        return ( bool ) defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
    }

    /**
     * Checks if the site debug display mode is enabled.
     * @since       3.5.3
     * @return      boolean
     */
    static public function isDebugDisplayEnabled() {
        return ( bool ) defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
    }

    /**
     * Returns the site language.
     *
     * @since       3.5.3
     * @return      string      The site language.
     */
    static public function getSiteLanguage( $sDefault='en_US' ) {
        return defined( 'WPLANG' ) && WPLANG ? WPLANG : $sDefault;
    }

}
