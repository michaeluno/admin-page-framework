<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods to retrieve various system information.
 *
 * @since       3.5.3
 * @extends     AdminPageFramework_Utility
 * @package     AdminPageFramework
 * @subpackage  Utility
 * @internal
 */
class AdminPageFramework_WPUtility_SiteInformation extends AdminPageFramework_WPUtility_Post {
    
    /**
     * Checks if the site debug mode is on.
     * 
     * @since       3.5.3
     * @return      boolean
     */
    static public function isDebugMode() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }
       
    
}