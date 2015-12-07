<?php
class AdminPageFramework_FrameworkUtility extends AdminPageFramework_WPUtility {
    static public function getFrameworkVersion() {
        return AdminPageFramework_Registry::getVersion();
    }
}