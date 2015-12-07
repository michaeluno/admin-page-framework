<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides utility methods which can be accessed among different components of the framework.
 *
 * @since           3.7.1
 * @extends         AdminPageFramework_WPUtility
 * @package         AdminPageFramework
 * @subpackage      Utility
 * @internal
 */
class AdminPageFramework_FrameworkUtility extends AdminPageFramework_WPUtility {
    
    /**
     * Returns the used framework version.
     * 
     * This is used by field type definition classes to determine whether their required framework version is used or not.
     * 
     * @since       3.7.1
     * @return      string
     */
    static public function getFrameworkVersion() {
        return AdminPageFramework_Registry::getVersion();
    }
    
    /**
     * Return the framwork name.
     * @since       3.7.1
     * @return      string
     */
    static public function getFrameworkName() {
        return AdminPageFramework_Registry::NAME;
    }
        
}