<?php
abstract class AdminPageFramework_Utility_VariableType extends AdminPageFramework_Utility_Deprecated {
    static public function isResourcePath($sPathOrURL) {
        if (file_exists($sPathOrURL)) {
            return true;
        }
        if (filter_var($sPathOrURL, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }
    static public function isNotNull($mValue = null) {
        return !is_null($mValue);
    }
    static public function isNumericInteger($mValue) {
        return is_numeric($mValue) && is_int($mValue + 0);
    }
}