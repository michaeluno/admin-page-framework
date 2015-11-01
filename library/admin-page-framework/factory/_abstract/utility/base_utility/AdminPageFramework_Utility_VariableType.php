<?php
abstract class AdminPageFramework_Utility_VariableType extends AdminPageFramework_Utility_Deprecated {
    public static function isNotNull($mValue = null) {
        return !is_null($mValue);
    }
    static public function isNumericInteger($mValue) {
        return is_numeric($mValue) && is_int($mValue + 0);
    }
}