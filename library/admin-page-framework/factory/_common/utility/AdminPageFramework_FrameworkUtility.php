<?php
class AdminPageFramework_FrameworkUtility extends AdminPageFramework_WPUtility {
    static public function getFrameworkVersion($bTrimDevVer = false) {
        $_sVersion = AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer ? self::getSuffixRemoved($_sVersion, '.dev') : $_sVersion;
    }
    static public function getFrameworkName() {
        return AdminPageFramework_Registry::NAME;
    }
}