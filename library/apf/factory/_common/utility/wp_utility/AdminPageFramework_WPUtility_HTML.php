<?php
/*
 * Admin Page Framework v3.9.0b18 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/admin-page-framework-compiler>
 * <https://en.michaeluno.jp/admin-page-framework>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AdminPageFramework_WPUtility_HTML extends AdminPageFramework_WPUtility_URL {
    public static function getAttributes(array $aAttributes)
    {
        $_sQuoteCharactor = "'";
        $_aOutput = array();
        foreach ($aAttributes as $_sAttribute => $_mProperty) {
            if (is_scalar($_mProperty)) {
                $_aOutput[] = "{$_sAttribute}={$_sQuoteCharactor}" . esc_attr($_mProperty) . "{$_sQuoteCharactor}";
            }
        }
        return implode(' ', $_aOutput);
    }
    public static function generateAttributes(array $aAttributes)
    {
        return self::getAttributes($aAttributes);
    }
    public static function getDataAttributes(array $aArray)
    {
        return self::getAttributes(self::getDataAttributeArray($aArray));
    }
    public static function generateDataAttributes(array $aArray)
    {
        return self::getDataAttributes($aArray);
    }
    public static function getHTMLTag($sTagName, array $aAttributes, $sValue=null)
    {
        $_sTag = tag_escape($sTagName);
        return null === $sValue ? "<" . $_sTag . " " . self::getAttributes($aAttributes) . " />" : "<" . $_sTag . " " . self::getAttributes($aAttributes) . ">" . $sValue . "</{$_sTag}>";
    }
    public static function generateHTMLTag($sTagName, array $aAttributes, $sValue=null)
    {
        return self::getHTMLTag($sTagName, $aAttributes, $sValue);
    }
}
