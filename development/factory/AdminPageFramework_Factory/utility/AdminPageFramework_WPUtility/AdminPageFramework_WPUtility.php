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
 * @since 2.0.0
 * @extends AdminPageFramework_Utility
 * @package AdminPageFramework
 * @subpackage Utility
 * @internal
 */
class AdminPageFramework_WPUtility extends AdminPageFramework_WPUtility_SystemInformation {
    
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
     * Indicates whether the flushing rewrite rules has been performed or not.
     * @since       3.1.5
     */
    static private $_bIsFlushed;
    
    /**
     * Flushes the site rewrite rules.
     *
     * The method ensures it is done mo more than once in a page load.
     * 
     * @since       3.1.5
     * @todo        Check if capitalizing the name is necessary or not.
     */
    static public function FlushRewriteRules() {
        
        $_bIsFlushed = isset( self::$_bIsFlushed ) ? self::$_bIsFlushed : false;
        if ( $_bIsFlushed ) {
            return;
        }
        flush_rewrite_rules();
        self::$_bIsFlushed = true;
        
    }    
    
}