<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_FrameworkUtility extends AdminPageFramework_WPUtility {
    public static function showDeprecationNotice($sDeprecated, $sAlternative='', $sProgramName='')
    {
        $sProgramName = $sProgramName ? $sProgramName : self::getFrameworkName();
        parent::showDeprecationNotice($sDeprecated, $sAlternative, $sProgramName);
    }
    public static function sortAdminSubMenu()
    {
        if (self::hasBeenCalled(__METHOD__)) {
            return;
        }
        foreach (( array ) $GLOBALS[ '_apf_sub_menus_to_sort' ] as $_sIndex => $_sMenuSlug) {
            if (! isset($GLOBALS[ 'submenu' ][ $_sMenuSlug ])) {
                continue;
            }
            ksort($GLOBALS[ 'submenu' ][ $_sMenuSlug ]);
            unset($GLOBALS[ '_apf_sub_menus_to_sort' ][ $_sIndex ]);
        }
    }
    public static function getFrameworkVersion($bTrimDevVer=false)
    {
        $_sVersion = AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer ? self::getSuffixRemoved($_sVersion, '.dev') : $_sVersion;
    }
    public static function getFrameworkName()
    {
        return AdminPageFramework_Registry::NAME;
    }
    public static function getFrameworkNameVersion()
    {
        return self::getFrameworkName() . ' ' . self::getFrameworkVersion();
    }
}
