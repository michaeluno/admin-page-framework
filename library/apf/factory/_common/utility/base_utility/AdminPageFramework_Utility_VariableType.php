<?php 
/**
	Admin Page Framework v3.9.0b10 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
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
    