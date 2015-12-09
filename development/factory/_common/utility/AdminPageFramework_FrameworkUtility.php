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
     * @since       3.7.2       Added the `$bTrimDevVer` parameter.
     * @param       boolean     $bTrimDevVer           Whether the `.dev` suffix shuold be removed or not.
     * @return      string
     */
    static public function getFrameworkVersion( $bTrimDevVer=false ) {
        $_sVersion = AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer
            ? self::getSuffixRemoved( $_sVersion, '.dev' )
            : $_sVersion;
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