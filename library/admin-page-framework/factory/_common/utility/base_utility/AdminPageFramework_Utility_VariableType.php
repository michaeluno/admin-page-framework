<?php
abstract class AdminPageFramework_Utility_VariableType extends AdminPageFramework_Utility_Deprecated {
    static public function isResourcePath($sPathOrURL) {
        if (defined('PHP_MAXPATHLEN') && strlen($sPathOrURL) > PHP_MAXPATHLEN) {
            return ( boolean )filter_var($sPathOrURL, FILTER_VALIDATE_URL);
        }
        if (file_exists($sPathOrURL)) {
            return true;
        }
        return ( boolean )filter_var($sPathOrURL, FILTER_VALIDATE_URL);
    }
    static public function isNotNull($mValue = null) {
        return !is_null($mValue);
    }
    static public function isNumericInteger($mValue) {
        return is_numeric($mValue) && is_int($mValue + 0);
    }
}