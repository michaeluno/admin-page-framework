<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2016 Michael Uno; Licensed MIT
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
     * Sorts admin sub-menu items.
     * 
     * @since       3.7.4
     * @return      void
     */
    static public function sortAdminSubMenu() {
        
        // This method is only enough to be called only once site-wide per page load.
        if ( self::hasBeenCalled( __METHOD__ ) ) {
            return;
        }

        foreach( ( array ) $GLOBALS[ '_apf_sub_menus_to_sort' ] as $_sIndex => $_sMenuSlug ) {
            if ( ! isset( $GLOBALS[ 'submenu' ][ $_sMenuSlug ] ) ) {
                continue;
            }
            ksort( $GLOBALS[ 'submenu' ][ $_sMenuSlug ] );
            unset( $GLOBALS[ '_apf_sub_menus_to_sort' ][ $_sIndex ] );
        }
        
    }
    
    /**
     * Returns the used framework version.
     * 
     * This is used by field type definition classes to determine whether their required framework version is used or not.
     * 
     * @since       3.7.1
     * @since       3.7.2       Added the `$bTrimDevVer` parameter.
     * @param       boolean     $bTrimDevVer           Whether the `.dev` suffix should be removed or not.
     * @return      string
     */
    static public function getFrameworkVersion( $bTrimDevVer=false ) {
        $_sVersion = AdminPageFramework_Registry::getVersion();

        return $bTrimDevVer
            ? self::getSuffixRemoved( $_sVersion, '.dev' )
            : $_sVersion;
    }
    
    /**
     * Return the framework name.
     * @since       3.7.1
     * @return      string
     */
    static public function getFrameworkName() {
        return AdminPageFramework_Registry::NAME;
    }
        
}
